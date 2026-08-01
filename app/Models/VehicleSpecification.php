<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleSpecification extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_brand_id',
        'vehicle_model_id',
        'vehicle_type_id',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(VehicleBrand::class, 'vehicle_brand_id');
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(VehicleModel::class, 'vehicle_model_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type_id');
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function servicePrices(): HasMany
    {
        return $this->hasMany(ServiceVehicleTypePrice::class, 'vehicle_specification_id');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy(
                VehicleBrand::select('name')
                    ->whereColumn('vehicle_brands.id', 'vehicle_specifications.vehicle_brand_id')
                    ->limit(1)
            )
            ->orderBy(
                VehicleModel::select('name')
                    ->whereColumn('vehicle_models.id', 'vehicle_specifications.vehicle_model_id')
                    ->limit(1)
            );
    }

    public function getLabelAttribute(): string
    {
        return trim(sprintf(
            '%s / %s / %s',
            $this->brand?->name ?? 'Sin marca',
            $this->model?->name ?? 'Sin modelo',
            $this->type?->name ?? 'Sin tipo'
        ));
    }
}
