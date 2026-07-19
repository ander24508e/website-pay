<?php

namespace App\Services\Sales;

use App\Models\Sale;
use App\Services\Inventory\InventoryService;

class DiscountSaleInventoryService
{
    public function __construct(private readonly InventoryService $inventoryService)
    {
    }

    public function discount(Sale $sale): void
    {
        $this->inventoryService->discountSale($sale);
    }
}
