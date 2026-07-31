<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CatalogItemVariant extends Model
{
    protected $fillable = [
        'catalog_item_id',
        'name',
        'sku',
        'price',
        'cost_price',
        'stock',
        'min_stock',
        'active',
        'is_default',
        'metadata',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock' => 'integer',
        'min_stock' => 'integer',
        'active' => 'boolean',
        'is_default' => 'boolean',
        'metadata' => 'array',
    ];

    public function item()
    {
        return $this->belongsTo(CatalogItem::class, 'catalog_item_id');
    }

    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class, 'catalog_item_variant_id');
    }

    public function locationStocks()
    {
        return $this->hasMany(InventoryStock::class, 'catalog_item_variant_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('is_default')->orderBy('name');
    }
}
