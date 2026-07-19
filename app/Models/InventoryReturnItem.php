<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryReturnItem extends Model
{
    protected $fillable = [
        'inventory_return_id',
        'catalog_item_variant_id',
        'quantity',
        'unit_cost',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_cost' => 'decimal:2',
    ];

    public function inventoryReturn()
    {
        return $this->belongsTo(InventoryReturn::class);
    }

    public function variant()
    {
        return $this->belongsTo(CatalogItemVariant::class, 'catalog_item_variant_id');
    }
}
