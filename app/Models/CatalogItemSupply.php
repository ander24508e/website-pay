<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatalogItemSupply extends Model
{
    protected $fillable = [
        'catalog_item_id',
        'service_vehicle_type_price_id',
        'catalog_item_variant_id',
        'quantity',
        'unit',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(CatalogItem::class, 'catalog_item_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(CatalogItemVariant::class, 'catalog_item_variant_id');
    }

    public function serviceVehiclePrice(): BelongsTo
    {
        return $this->belongsTo(ServiceVehicleTypePrice::class, 'service_vehicle_type_price_id');
    }
}
