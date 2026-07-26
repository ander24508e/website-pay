<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatalogItemBundleItem extends Model
{
    protected $fillable = [
        'catalog_item_bundle_id',
        'catalog_item_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function bundle(): BelongsTo
    {
        return $this->belongsTo(CatalogItemBundle::class, 'catalog_item_bundle_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(CatalogItem::class, 'catalog_item_id');
    }
}
