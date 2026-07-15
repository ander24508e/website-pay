<?php

namespace App\Services\Sales;

use App\Models\InventoryMovement;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\CatalogItemVariant;
use Illuminate\Validation\ValidationException;

class DiscountSaleInventoryService
{
    public function discount(Sale $sale): void
    {
        $sale->loadMissing(['items.catalogItem', 'items.variant']);

        foreach ($sale->items as $saleItem) {
            $this->discountItem($sale, $saleItem);
        }
    }

    private function discountItem(Sale $sale, SaleItem $saleItem): void
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

        $lockedVariant = CatalogItemVariant::query()
            ->whereKey($variant->id)
            ->lockForUpdate()
            ->firstOrFail();

        $stockBefore = (int) $lockedVariant->stock;
        $quantity = (int) $saleItem->quantity;

        if ($stockBefore < $quantity) {
            throw ValidationException::withMessages([
                'items' => 'No hay stock suficiente para completar el descuento de inventario.',
            ]);
        }

        $stockAfter = max(0, $stockBefore - $quantity);

        $lockedVariant->update(['stock' => $stockAfter]);

        InventoryMovement::create([
            'catalog_item_variant_id' => $variant->id,
            'user_id' => auth()->id(),
            'sale_id' => $sale->id,
            'sale_item_id' => $saleItem->id,
            'type' => 'out',
            'quantity' => $quantity,
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
            'notes' => 'Venta sistema #' . $sale->id,
        ]);
    }
}
