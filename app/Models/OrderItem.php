<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    // Relación polimórfica — devuelve Product o Service
    public function itemable()
    {
        return $this->morphTo();
    }
}