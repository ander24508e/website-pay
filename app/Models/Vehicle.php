<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vehicle_brand_id',
        'vehicle_model_id',
        'vehicle_type_id',
        'vehicle_specification_id',
        'plate',
        'color',
        'year',
        'observations',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'year' => 'integer',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

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

    public function specification(): BelongsTo
    {
        return $this->belongsTo(VehicleSpecification::class, 'vehicle_specification_id');
    }

    public function resolvedBrand(): ?VehicleBrand
    {
        return $this->specification?->brand ?? $this->brand;
    }

    public function resolvedModel(): ?VehicleModel
    {
        return $this->specification?->model ?? $this->model;
    }

    public function resolvedType(): ?VehicleType
    {
        return $this->specification?->type ?? $this->type;
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
