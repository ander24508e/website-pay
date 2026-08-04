<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceVehicleTypePrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'catalog_item_id',
        'vehicle_specification_id',
        'vehicle_type_id',
        'price',
        'duration_minutes',
        'description',
        'active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_minutes' => 'integer',
        'active' => 'boolean',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(CatalogItem::class, 'catalog_item_id');
    }

    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type_id');
    }

    public function vehicleSpecification(): BelongsTo
    {
        return $this->belongsTo(VehicleSpecification::class, 'vehicle_specification_id');
    }

    public function supplies(): HasMany
    {
        return $this->hasMany(CatalogItemSupply::class, 'service_vehicle_type_price_id');
    }

    public function getVehicleLabelAttribute(): string
    {
        return $this->vehicleSpecification?->label
            ?? $this->vehicleType?->name
            ?? 'Vehiculo eliminado';
    }
}
