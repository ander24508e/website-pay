<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryPeriod extends Model
{
    protected $fillable = [
        'empresa_id',
        'user_id',
        'date_from',
        'date_to',
        'status',
        'variants_count',
        'total_units',
        'total_value',
        'notes',
        'closed_at',
    ];

    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
        'variants_count' => 'integer',
        'total_units' => 'integer',
        'total_value' => 'decimal:2',
        'closed_at' => 'datetime',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
