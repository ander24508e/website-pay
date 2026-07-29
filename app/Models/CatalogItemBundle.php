<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CatalogItemBundle extends Model
{
    protected $fillable = [
        'empresa_id',
        'name',
        'slug',
        'description',
        'price',
        'active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function bundleItems()
    {
        return $this->hasMany(CatalogItemBundleItem::class);
    }

    public function items()
    {
        return $this->belongsToMany(CatalogItem::class, 'catalog_item_bundle_items')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('name');
    }
}
