<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'empresa_id',
        'name',
        'document',
        'phone',
        'email',
        'address',
        'notes',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('name');
    }
}
