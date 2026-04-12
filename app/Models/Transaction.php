<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'order_id',
        'payphone_ref',
        'amount',
        'status',
        'response_payload',
        'client_transaction_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'response_payload' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
