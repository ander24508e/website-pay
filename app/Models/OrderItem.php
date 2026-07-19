<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'itemable_type',
        'itemable_id',
        'catalog_item_variant_id',
        'vehicle_id',
        'vehicle_type_id',
        'quantity',
        'unit_price',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function itemable()
    {
        return $this->morphTo();
    }

    public function variant()
    {
        return $this->belongsTo(CatalogItemVariant::class, 'catalog_item_variant_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function variant()
    {
        return $this->belongsTo(CatalogItemVariant::class, 'catalog_item_variant_id');
    }

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function getItemTypeLabelAttribute(): string
    {
        if ($this->itemable instanceof CatalogItem) {
            return $this->itemable->type->name ?? 'Catalogo';
        }

        if (!$this->itemable_type) {
            return 'Catalogo';
        }

        return Str::headline(class_basename($this->itemable_type));
    }

    public function getItemDisplayNameAttribute(): string
    {
        return $this->itemable->name ?? 'Item eliminado';
    }

    public function getVehicleDisplayAttribute(): ?string
    {
        if ($this->vehicle) {
            return trim(sprintf(
                '%s - %s %s',
                $this->vehicle->plate,
                $this->vehicle->resolvedBrand()?->name ?? '',
                $this->vehicle->resolvedModel()?->name ?? ''
            ));
        }

        return $this->vehicleType?->name;
    }
}
