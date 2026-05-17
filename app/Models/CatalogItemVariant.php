<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CatalogItemVariant extends Model
{
    protected $fillable = [
        'catalog_item_id',
        'name',
        'presentation',
        'specification',
        'sku',
        'price',
        'stock',
        'active',
        'is_default',
        'sort_order',
        'metadata',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'active' => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
        'metadata' => 'array',
    ];

    public function item()
    {
        return $this->belongsTo(CatalogItem::class, 'catalog_item_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
