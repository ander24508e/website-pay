<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CatalogItem extends Model
{
    protected $fillable = [
        'empresa_id',
        'catalog_type_id',
        'catalog_category_id',
        'legacy_source_type',
        'legacy_source_id',
        'name',
        'slug',
        'description',
        'base_price',
        'duration_minutes',
        'image',
        'active',
        'featured',
        'purchasable',
        'reservable',
        'uses_inventory',
        'metadata',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'duration_minutes' => 'integer',
        'active' => 'boolean',
        'featured' => 'boolean',
        'purchasable' => 'boolean',
        'reservable' => 'boolean',
        'uses_inventory' => 'boolean',
        'metadata' => 'array',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function type()
    {
        return $this->belongsTo(CatalogType::class, 'catalog_type_id');
    }

    public function category()
    {
        return $this->belongsTo(CatalogCategory::class, 'catalog_category_id');
    }

    public function variants()
    {
        return $this->hasMany(CatalogItemVariant::class)->orderByDesc('is_default')->orderBy('name');
    }

    public function activeVariants()
    {
        return $this->hasMany(CatalogItemVariant::class)->where('active', true)->orderByDesc('is_default')->orderBy('name');
    }

    public function vehicleTypePrices()
    {
        return $this->hasMany(ServiceVehicleTypePrice::class)->orderBy('vehicle_type_id')->orderBy('id');
    }

    public function supplies()
    {
        return $this->hasMany(CatalogItemSupply::class);
    }

    public function bundleItems()
    {
        return $this->hasMany(CatalogItemBundleItem::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('name');
    }

    public function getDisplayPriceAttribute(): float
    {
        if (!$this->uses_inventory) {
            $servicePrice = $this->relationLoaded('vehicleTypePrices')
                ? $this->vehicleTypePrices->where('active', true)->pluck('price')->filter(fn ($price) => $price !== null)->min()
                : $this->vehicleTypePrices()->where('active', true)->whereNotNull('price')->min('price');

            return (float) ($servicePrice ?? $this->base_price ?? 0);
        }

        $variantPrice = $this->relationLoaded('activeVariants')
            ? $this->activeVariants->min('price')
            : $this->activeVariants()->min('price');

        return (float) ($variantPrice ?? $this->base_price ?? 0);
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return Storage::url($this->image);
        }

        return asset('Images/empresa-logo.jpg');
    }
}
