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
}
