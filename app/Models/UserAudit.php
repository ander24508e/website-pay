<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAudit extends Model
{
    protected $fillable = [
        'target_user_id',
        'actor_user_id',
        'action',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}
