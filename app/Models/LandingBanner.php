<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LandingBanner extends Model
{
    protected $fillable = [
        'empresa_id',
        'titulo',
        'texto',
        'imagen',
        'boton_texto',
        'boton_link',
        'orden',
        'activo',
        'es_principal',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'es_principal' => 'boolean',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('es_principal')->orderBy('orden')->orderByDesc('created_at');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function getImagenUrlAttribute(): string
    {
        if ($this->imagen && Storage::disk('public')->exists($this->imagen)) {
            return Storage::url($this->imagen);
        }

        return asset('Images/empresa-logo.jpg');
    }
}
