<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceVehicleTypePrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'catalog_item_id',
        'vehicle_specification_id',
        'price',
        'duration_minutes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_minutes' => 'integer',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(CatalogItem::class, 'catalog_item_id');
    }

    public function vehicleSpecification(): BelongsTo
    {
        return $this->belongsTo(VehicleSpecification::class, 'vehicle_specification_id');
    }
}
