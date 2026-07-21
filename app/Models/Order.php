<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int|null $assigned_to
 * @property int|null $sale_id
 * @property string|null $work_status
 */
class Order extends Model
{
    use HasFactory;

    public const WORK_PENDING = 'pending';
    public const WORK_ARRIVED = 'arrived';
    public const WORK_IN_PROGRESS = 'in_progress';
    public const WORK_READY = 'ready';
    public const WORK_COMPLETED = 'completed';
    public const WORK_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'assigned_to',
        'sale_id',
        'total',
        'status',
        'work_status',
        'order_type',
        'payphone_transaction_id',
        'scheduled_at',
        'arrived_at',
        'started_at',
        'ready_at',
        'completed_at',
        'cancelled_at',
        'work_notes',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'scheduled_at' => 'datetime',
        'arrived_at' => 'datetime',
        'started_at' => 'datetime',
        'ready_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public static function workStatusLabels(): array
    {
        return [
            self::WORK_PENDING => 'Pendiente',
            self::WORK_ARRIVED => 'Cliente llegó',
            self::WORK_IN_PROGRESS => 'En proceso',
            self::WORK_READY => 'Listo',
            self::WORK_COMPLETED => 'Completado',
            self::WORK_CANCELLED => 'Cancelado',
        ];
    }

    public static function workStatusBadges(): array
    {
        return [
            self::WORK_PENDING => 'bg-yellow-100 text-yellow-700',
            self::WORK_ARRIVED => 'bg-blue-100 text-blue-700',
            self::WORK_IN_PROGRESS => 'bg-indigo-100 text-indigo-700',
            self::WORK_READY => 'bg-emerald-100 text-emerald-700',
            self::WORK_COMPLETED => 'bg-green-100 text-green-700',
            self::WORK_CANCELLED => 'bg-gray-100 text-gray-600',
        ];
    }

    public function workTransitions(): array
    {
        return match ($this->work_status ?? self::WORK_PENDING) {
            self::WORK_PENDING => [
                self::WORK_ARRIVED => 'Cliente llegó',
                self::WORK_CANCELLED => 'Cancelar',
            ],
            self::WORK_ARRIVED => [
                self::WORK_IN_PROGRESS => 'Iniciar',
                self::WORK_CANCELLED => 'Cancelar',
            ],
            self::WORK_IN_PROGRESS => [
                self::WORK_READY => 'Marcar listo',
            ],
            self::WORK_READY => [
                self::WORK_COMPLETED => 'Completar',
            ],
            default => [],
        };
    }

    public function getWorkStatusLabelAttribute(): string
    {
        return self::workStatusLabels()[$this->work_status ?? self::WORK_PENDING] ?? (string) $this->work_status;
    }

    public function getWorkStatusBadgeAttribute(): string
    {
        return self::workStatusBadges()[$this->work_status ?? self::WORK_PENDING] ?? 'bg-gray-100 text-gray-600';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class)->latestOfMany();
    }
}
