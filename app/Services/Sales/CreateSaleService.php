<?php

namespace App\Services\Sales;

use App\Data\SaleData;
use App\Models\CatalogItem;
use App\Models\CatalogItemVariant;
use App\Models\CatalogType;
use App\Models\Order;
use App\Models\Sale;
use App\Services\ServiceVehiclePriceResolver;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateSaleService
{
    public function __construct(
        private readonly ServiceVehiclePriceResolver $priceResolver,
    ) {
    }

    public function create(SaleData $data): Sale
    {
        return DB::transaction(function () use ($data) {
            $resolvedItems = $this->resolveItems($data);
            $subtotal = round((float) collect($resolvedItems)->sum('subtotal'), 2);
            $taxTotal = round((float) collect($resolvedItems)->sum('tax_amount'), 2);
            $total = round($subtotal + $taxTotal, 2);

            $sale = Sale::create([
                'user_id' => $data->userId,
                'vehicle_id' => null,
                'attended_by' => $data->attendedBy ?: auth()->id(),
                'status' => Sale::STATUS_PENDING,
                'payment_status' => 'pending',
                'payment_method' => 'unassigned',
                'subtotal' => $subtotal,
                'discount' => 0,
                'tax_total' => $taxTotal,
                'total' => $total,
                'notes' => $data->notes,
            ]);

            $order = Order::create([
                'user_id' => $data->userId,
                'assigned_to' => $data->attendedBy ?: auth()->id(),
                'sale_id' => $sale->id,
                'total' => $total,
                'status' => 'pending',
                'work_status' => Order::WORK_PENDING,
                'order_type' => 'manual_sale',
                'scheduled_at' => $data->scheduledAt,
                'work_notes' => $data->notes,
            ]);

            foreach ($resolvedItems as $item) {
                $saleItem = $sale->items()->create($item['payload']);

                $order->items()->create([
                    'itemable_type' => CatalogItem::class,
                    'itemable_id' => $saleItem->catalog_item_id,
                    'catalog_item_variant_id' => $saleItem->catalog_item_variant_id,
                    'vehicle_id' => $saleItem->vehicle_id,
                    'vehicle_type_id' => $saleItem->vehicle_type_id,
                    'quantity' => $saleItem->quantity,
                    'unit_price' => $saleItem->unit_price,
                ]);
            }

            $sale->audits()->create([
                'user_id' => auth()->id(),
                'action' => 'sale.created_pending',
                'payload' => [
                    'order_id' => $order->id,
                    'scheduled_at' => $data->scheduledAt,
                    'total' => $total,
                ],
            ]);

            return $sale->load(['items', 'order']);
        });
    }

    private function resolveItems(SaleData $data): array
    {
        $resolved = [];

        foreach ($data->items as $index => $row) {
            $catalogItem = CatalogItem::query()
                ->with([
                    'type',
                    'activeVariants',
                    'vehicleTypePrices.vehicleType',
                    'vehicleTypePrices.vehicleSpecification.brand',
                    'vehicleTypePrices.vehicleSpecification.model',
                    'vehicleTypePrices.vehicleSpecification.type',
                ])
                ->where('active', true)
                ->where('purchasable', true)
                ->find((int) ($row['catalog_item_id'] ?? 0));

            if (!$catalogItem) {
                throw ValidationException::withMessages(["items.{$index}.catalog_item_id" => 'El ítem seleccionado no está disponible.']);
            }

            $quantity = max(1, (int) ($row['quantity'] ?? 1));
            $isProduct = ($catalogItem->type?->business_model ?? CatalogType::BUSINESS_MODEL_SERVICES) === CatalogType::BUSINESS_MODEL_PRODUCTS;
            $variant = null;
            $unitPrice = (float) $catalogItem->display_price;
            $vehicleContext = [];

            if ($isProduct) {
                $variant = $this->resolveVariant($catalogItem, $row['catalog_item_variant_id'] ?? null);

                if (!$variant) {
                    throw ValidationException::withMessages(["items.{$index}.catalog_item_variant_id" => 'Selecciona una presentación activa para este producto.']);
                }

                $unitPrice = (float) ($variant->price ?? 0);

                if ($catalogItem->uses_inventory && (int) $variant->stock < $quantity) {
                    throw ValidationException::withMessages(["items.{$index}.quantity" => 'No hay stock suficiente para este producto.']);
                }
            } else {
                $vehicleContext = $this->priceResolver->resolve(
                    $catalogItem,
                    !empty($row['vehicle_id']) ? (int) $row['vehicle_id'] : null,
                    !empty($row['vehicle_specification_id']) ? (int) $row['vehicle_specification_id'] : null,
                    $data->userId,
                    !empty($row['vehicle_type_id']) ? (int) $row['vehicle_type_id'] : null
                );
                $unitPrice = (float) $vehicleContext['price'];
            }

            $subtotal = round($unitPrice * $quantity, 2);

            $resolved[] = [
                'subtotal' => $subtotal,
                'tax_amount' => 0,
                'payload' => [
                    'catalog_item_id' => $catalogItem->id,
                    'catalog_item_variant_id' => $isProduct ? $variant->id : null,
                    'vehicle_id' => !$isProduct && !empty($row['vehicle_id']) ? (int) $row['vehicle_id'] : null,
                    'vehicle_type_id' => !$isProduct ? ($vehicleContext['vehicle_type_id'] ?? null) : null,
                    'name_snapshot' => $catalogItem->name,
                    'type_snapshot' => $isProduct ? 'Producto' : 'Servicio',
                    'description_snapshot' => $catalogItem->description,
                    'code_snapshot' => $variant?->sku,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount_amount' => 0,
                    'tax_rate' => 0,
                    'tax_amount' => 0,
                    'subtotal' => $subtotal,
                    'total' => $subtotal,
                ],
            ];
        }

        return $resolved;
    }

    private function resolveVariant(CatalogItem $catalogItem, mixed $variantId): ?CatalogItemVariant
    {
        if ($variantId) {
            $variant = CatalogItemVariant::query()
                ->where('catalog_item_id', $catalogItem->id)
                ->where('active', true)
                ->find((int) $variantId);

            if (!$variant) {
                throw ValidationException::withMessages(['items' => 'La presentación seleccionada no pertenece al ítem o no está activa.']);
            }

            return $variant;
        }

        return $catalogItem->activeVariants->sortByDesc('is_default')->first();
    }
}
