<?php

namespace App\Services\Inventory;

use App\Models\CatalogItemVariant;
use App\Models\InventoryLocation;
use App\Models\InventoryMovement;
use App\Models\InventoryStock;
use App\Models\InventoryTransfer;
use App\Models\InventoryTransferItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    public function applyMovement(
        CatalogItemVariant $variant,
        string $type,
        int $quantity,
        ?string $notes = null,
        array $context = []
    ): InventoryMovement {
        if (!in_array($type, ['in', 'out', 'adjust'], true)) {
            throw ValidationException::withMessages(['type' => 'Tipo de movimiento invalido.']);
        }

        if ($quantity < 0 || ($type !== 'adjust' && $quantity < 1)) {
            throw ValidationException::withMessages(['quantity' => 'La cantidad debe ser mayor a cero.']);
        }

        return DB::transaction(function () use ($variant, $type, $quantity, $notes, $context) {
            $lockedVariant = CatalogItemVariant::query()
                ->whereKey($variant->id)
                ->lockForUpdate()
                ->firstOrFail();

            $stockBefore = (int) ($lockedVariant->stock ?? 0);
            $previousUnitCost = round((float) ($lockedVariant->cost_price ?? 0), 2);
            $movementUnitCost = $this->resolveMovementUnitCost($type, $context, $previousUnitCost);
            $location = $context['location'] ?? null;
            $locationId = $context['inventory_location_id'] ?? $context['location_id'] ?? null;
            $locationStockBefore = null;
            $locationStockAfter = null;

            $stockAfter = match ($type) {
                'in' => $stockBefore + $quantity,
                'out' => $this->resolveOutgoingStock($stockBefore, $quantity),
                'adjust' => $quantity,
            };

            $balanceUnitCost = $this->resolveBalanceUnitCost($type, $stockBefore, $stockAfter, $previousUnitCost, $movementUnitCost, $quantity);
            $balanceQuantity = $stockAfter;
            $balanceTotalCost = round($balanceQuantity * $balanceUnitCost, 2);
            $movementTotalCost = $this->resolveMovementTotalCost($type, $stockBefore, $stockAfter, $quantity, $movementUnitCost);

            $lockedVariant->update([
                'stock' => $stockAfter,
                'cost_price' => $balanceUnitCost,
            ]);

            if ($location || $locationId) {
                $location = $location instanceof InventoryLocation
                    ? $location
                    : InventoryLocation::query()->findOrFail((int) $locationId);

                $stockRow = InventoryStock::query()
                    ->where('inventory_location_id', $location->id)
                    ->where('catalog_item_variant_id', $lockedVariant->id)
                    ->lockForUpdate()
                    ->first();

                if (!$stockRow) {
                    $stockRow = InventoryStock::create([
                        'inventory_location_id' => $location->id,
                        'catalog_item_variant_id' => $lockedVariant->id,
                        'quantity' => 0,
                        'min_stock' => (int) ($lockedVariant->min_stock ?? 0),
                    ]);
                    $stockRow->refresh();
                }

                $locationStockBefore = (int) $stockRow->quantity;
                $locationStockAfter = match ($type) {
                    'in' => $locationStockBefore + $quantity,
                    'out' => $this->resolveOutgoingStock($locationStockBefore, $quantity),
                    'adjust' => $quantity,
                };

                $stockRow->update(['quantity' => $locationStockAfter]);
            }

            return InventoryMovement::create([
                'catalog_item_variant_id' => $lockedVariant->id,
                'inventory_location_id' => $location?->id,
                'from_location_id' => $context['from_location_id'] ?? null,
                'to_location_id' => $context['to_location_id'] ?? null,
                'user_id' => $context['user_id'] ?? auth()->id(),
                'sale_id' => $context['sale_id'] ?? null,
                'sale_item_id' => $context['sale_item_id'] ?? null,
                'order_id' => $context['order_id'] ?? null,
                'order_item_id' => $context['order_item_id'] ?? null,
                'purchase_id' => $context['purchase_id'] ?? null,
                'purchase_item_id' => $context['purchase_item_id'] ?? null,
                'inventory_transfer_id' => $context['inventory_transfer_id'] ?? null,
                'inventory_transfer_item_id' => $context['inventory_transfer_item_id'] ?? null,
                'type' => $type,
                'reason' => $context['reason'] ?? null,
                'reference' => $context['reference'] ?? null,
                'quantity' => $quantity,
                'unit_cost' => $movementUnitCost,
                'total_cost' => $movementTotalCost,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'balance_quantity' => $balanceQuantity,
                'balance_unit_cost' => $balanceUnitCost,
                'balance_total_cost' => $balanceTotalCost,
                'notes' => $this->movementNotes($notes, $locationStockBefore, $locationStockAfter),
            ]);
        });
    }

    public function transfer(
        InventoryTransfer $transfer,
        InventoryTransferItem $item,
        InventoryLocation $fromLocation,
        InventoryLocation $toLocation,
    ): void {
        DB::transaction(function () use ($transfer, $item, $fromLocation, $toLocation) {
            $variant = $item->variant()->lockForUpdate()->firstOrFail();

            $this->applyMovement(
                $variant,
                'out',
                (int) $item->quantity,
                'Transferencia salida #' . $transfer->id,
                [
                    'inventory_location_id' => $fromLocation->id,
                    'from_location_id' => $fromLocation->id,
                    'to_location_id' => $toLocation->id,
                    'inventory_transfer_id' => $transfer->id,
                    'inventory_transfer_item_id' => $item->id,
                    'reason' => 'transferencia',
                    'reference' => $transfer->reference ?: 'transfer:' . $transfer->id,
                    'unit_cost' => $variant->cost_price,
                ]
            );

            $this->applyMovement(
                $variant,
                'in',
                (int) $item->quantity,
                'Transferencia entrada #' . $transfer->id,
                [
                    'inventory_location_id' => $toLocation->id,
                    'from_location_id' => $fromLocation->id,
                    'to_location_id' => $toLocation->id,
                    'inventory_transfer_id' => $transfer->id,
                    'inventory_transfer_item_id' => $item->id,
                    'reason' => 'transferencia',
                    'reference' => $transfer->reference ?: 'transfer:' . $transfer->id,
                    'unit_cost' => $variant->cost_price,
                ]
            );
        });
    }

    public function discountSale(Sale $sale): void
    {
        DB::transaction(function () use ($sale) {
            $sale->loadMissing(['items.catalogItem', 'items.variant']);

            foreach ($sale->items as $saleItem) {
                $this->discountSaleItem($sale, $saleItem);
            }
        });
    }

    public function discountOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->loadMissing(['items.itemable', 'items.variant']);

            foreach ($order->items as $orderItem) {
                $this->discountOrderItem($order, $orderItem);
            }
        });
    }

    public function reverseMovement(InventoryMovement $movement, ?int $userId = null): InventoryMovement
    {
        if ($movement->voided_at) {
            throw ValidationException::withMessages([
                'movement' => 'Este movimiento ya fue anulado.',
            ]);
        }

        $movement->loadMissing('variant');

        return DB::transaction(function () use ($movement, $userId) {
            $reverseType = match ($movement->type) {
                'in' => 'out',
                'out' => 'in',
                'adjust' => 'adjust',
            };

            $reverseQuantity = $movement->type === 'adjust'
                ? max(0, (int) ($movement->stock_before ?? 0))
                : (int) $movement->quantity;

            $reversal = $this->applyMovement(
                $movement->variant,
                $reverseType,
                $reverseQuantity,
                'Anulacion del movimiento #' . $movement->id,
                [
                    'inventory_location_id' => $movement->inventory_location_id,
                    'from_location_id' => $movement->to_location_id,
                    'to_location_id' => $movement->from_location_id,
                    'user_id' => $userId ?? auth()->id(),
                    'reason' => 'anulacion',
                    'reference' => 'movement:' . $movement->id,
                    'unit_cost' => $movement->unit_cost,
                ]
            );

            $movement->update([
                'voided_at' => now(),
                'voided_by' => $userId ?? auth()->id(),
                'reversal_movement_id' => $reversal->id,
                'notes' => trim(($movement->notes ? $movement->notes . ' | ' : '') . 'ANULADO'),
            ]);

            return $reversal;
        });
    }

    private function discountSaleItem(Sale $sale, SaleItem $saleItem): void
    {
        $variant = $saleItem->variant;
        $catalogItem = $saleItem->catalogItem;

        if (!$variant || !$catalogItem || !$catalogItem->uses_inventory) {
            return;
        }

        $alreadyDiscounted = InventoryMovement::query()
            ->where('sale_item_id', $saleItem->id)
            ->where('type', 'out')
            ->exists();

        if ($alreadyDiscounted) {
            return;
        }

        $this->applyMovement(
            $variant,
            'out',
            (int) $saleItem->quantity,
            'Venta sistema #' . $sale->id,
            [
                'sale_id' => $sale->id,
                'sale_item_id' => $saleItem->id,
                'reason' => 'venta',
                'reference' => 'sale:' . $sale->id,
                'unit_cost' => $variant->cost_price,
            ]
        );
    }

    private function discountOrderItem(Order $order, OrderItem $orderItem): void
    {
        $catalogItem = $orderItem->itemable;

        if (!$catalogItem || !($catalogItem->uses_inventory ?? false) || !$orderItem->variant) {
            return;
        }

        $alreadyDiscounted = InventoryMovement::query()
            ->where('order_item_id', $orderItem->id)
            ->where('type', 'out')
            ->exists();

        if ($alreadyDiscounted) {
            return;
        }

        $this->applyMovement(
            $orderItem->variant,
            'out',
            (int) $orderItem->quantity,
            'Orden web #' . $order->id,
            [
                'order_id' => $order->id,
                'order_item_id' => $orderItem->id,
                'reason' => 'orden_web',
                'reference' => 'order:' . $order->id,
                'unit_cost' => $orderItem->variant->cost_price,
            ]
        );
    }

    private function resolveOutgoingStock(int $stockBefore, int $quantity): int
    {
        if ($stockBefore < $quantity) {
            throw ValidationException::withMessages([
                'quantity' => 'No hay stock suficiente para registrar esta salida.',
            ]);
        }

        return $stockBefore - $quantity;
    }

    private function movementNotes(?string $notes, ?int $locationStockBefore, ?int $locationStockAfter): ?string
    {
        $notes = trim((string) $notes);

        if ($locationStockBefore === null || $locationStockAfter === null) {
            return $notes ?: null;
        }

        $locationNote = "Ubicacion: {$locationStockBefore} -> {$locationStockAfter}";

        return $notes !== '' ? $notes . ' | ' . $locationNote : $locationNote;
    }

    private function resolveMovementUnitCost(string $type, array $context, float $previousUnitCost): float
    {
        if (isset($context['unit_cost']) && is_numeric($context['unit_cost'])) {
            return round((float) $context['unit_cost'], 2);
        }

        return $previousUnitCost;
    }

    private function resolveBalanceUnitCost(
        string $type,
        int $stockBefore,
        int $stockAfter,
        float $previousUnitCost,
        float $movementUnitCost,
        int $quantity
    ): float {
        if ($stockAfter <= 0) {
            return $movementUnitCost > 0 ? $movementUnitCost : $previousUnitCost;
        }

        if ($type === 'in') {
            $previousValue = $stockBefore * $previousUnitCost;
            $incomingValue = $quantity * $movementUnitCost;

            return round(($previousValue + $incomingValue) / $stockAfter, 2);
        }

        if ($type === 'adjust') {
            return $movementUnitCost > 0 ? $movementUnitCost : $previousUnitCost;
        }

        return $previousUnitCost;
    }

    private function resolveMovementTotalCost(string $type, int $stockBefore, int $stockAfter, int $quantity, float $unitCost): float
    {
        $costQuantity = $type === 'adjust'
            ? abs($stockAfter - $stockBefore)
            : $quantity;

        return round($costQuantity * $unitCost, 2);
    }
}
