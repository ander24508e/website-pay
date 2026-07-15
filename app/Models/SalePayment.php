<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalePayment extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_FAILED = 'failed';

    public const METHOD_CASH = 'cash';
    public const METHOD_PAYPHONE = 'payphone';
    public const METHOD_TRANSFER = 'transfer';
    public const METHOD_CARD = 'card';
    public const METHOD_CREDIT = 'credit';

    protected $fillable = [
        'sale_id',
        'method',
        'status',
        'amount',
        'received_amount',
        'change_amount',
        'transaction_id',
        'bank',
        'reference',
        'proof_path',
        'authorization_code',
        'due_date',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'received_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'due_date' => 'date',
        'metadata' => 'array',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}
