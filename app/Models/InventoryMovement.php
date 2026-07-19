<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    protected $fillable = [
        'catalog_item_variant_id',
        'inventory_location_id',
        'from_location_id',
        'to_location_id',
        'user_id',
        'sale_id',
        'sale_item_id',
        'order_id',
        'order_item_id',
        'purchase_id',
        'purchase_item_id',
        'inventory_transfer_id',
        'inventory_transfer_item_id',
        'inventory_return_id',
        'inventory_return_item_id',
        'inventory_count_id',
        'inventory_count_item_id',
        'type',
        'reason',
        'reference',
        'batch_number',
        'expires_at',
        'quantity',
        'unit_cost',
        'total_cost',
        'stock_before',
        'stock_after',
        'balance_quantity',
        'balance_unit_cost',
        'balance_total_cost',
        'notes',
        'voided_at',
        'voided_by',
        'reversal_movement_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'stock_before' => 'integer',
        'stock_after' => 'integer',
        'balance_quantity' => 'integer',
        'balance_unit_cost' => 'decimal:2',
        'balance_total_cost' => 'decimal:2',
        'expires_at' => 'date',
        'voided_at' => 'datetime',
    ];

    public function variant()
    {
        return $this->belongsTo(CatalogItemVariant::class, 'catalog_item_variant_id');
    }

    public function location()
    {
        return $this->belongsTo(InventoryLocation::class, 'inventory_location_id');
    }

    public function fromLocation()
    {
        return $this->belongsTo(InventoryLocation::class, 'from_location_id');
    }

    public function toLocation()
    {
        return $this->belongsTo(InventoryLocation::class, 'to_location_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function saleItem()
    {
        return $this->belongsTo(SaleItem::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function purchaseItem()
    {
        return $this->belongsTo(PurchaseItem::class);
    }

    public function transfer()
    {
        return $this->belongsTo(InventoryTransfer::class, 'inventory_transfer_id');
    }

    public function transferItem()
    {
        return $this->belongsTo(InventoryTransferItem::class, 'inventory_transfer_item_id');
    }

    public function inventoryReturn()
    {
        return $this->belongsTo(InventoryReturn::class);
    }

    public function inventoryReturnItem()
    {
        return $this->belongsTo(InventoryReturnItem::class);
    }

    public function inventoryCount()
    {
        return $this->belongsTo(InventoryCount::class);
    }

    public function inventoryCountItem()
    {
        return $this->belongsTo(InventoryCountItem::class);
    }

    public function voidedBy()
    {
        return $this->belongsTo(User::class, 'voided_by');
    }

    public function reversalMovement()
    {
        return $this->belongsTo(self::class, 'reversal_movement_id');
    }
}
