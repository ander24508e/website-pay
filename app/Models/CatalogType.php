<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CatalogType extends Model
{
    public const BUSINESS_MODEL_SERVICES = 'services';
    public const BUSINESS_MODEL_PRODUCTS = 'products';

    protected $fillable = [
        'empresa_id',
        'name',
        'slug',
        'description',
        'business_model',
        'sort_order',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function categories()
    {
        return $this->hasMany(CatalogCategory::class)->orderBy('sort_order')->orderBy('name');
    }

    public function items()
    {
        return $this->hasMany(CatalogItem::class)->orderBy('sort_order')->orderBy('name');
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
