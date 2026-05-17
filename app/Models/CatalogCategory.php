<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CatalogCategory extends Model
{
    protected $fillable = [
        'empresa_id',
        'catalog_type_id',
        'name',
        'slug',
        'description',
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

    public function type()
    {
        return $this->belongsTo(CatalogType::class, 'catalog_type_id');
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
