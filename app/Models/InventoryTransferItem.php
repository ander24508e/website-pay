<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryTransferItem extends Model
{
    protected $fillable = [
        'inventory_transfer_id',
        'catalog_item_variant_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function transfer()
    {
        return $this->belongsTo(InventoryTransfer::class, 'inventory_transfer_id');
    }

    public function variant()
    {
        return $this->belongsTo(CatalogItemVariant::class, 'catalog_item_variant_id');
    }
}
