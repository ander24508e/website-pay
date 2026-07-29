<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class VehicleType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function vehicles(): HasManyThrough
    {
        return $this->hasManyThrough(
            Vehicle::class,
            VehicleSpecification::class,
            'vehicle_type_id',
            'vehicle_specification_id',
            'id',
            'id'
        );
    }

    public function servicePrices(): HasMany
    {
        return $this->hasMany(ServiceVehicleTypePrice::class);
    }

    public function specifications(): HasMany
    {
        return $this->hasMany(VehicleSpecification::class);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('name');
    }
}
