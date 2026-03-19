<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Empresa extends Model
{
    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'logo'
    ];

    /**
     * Accessor para la URL del logo
     */
    public function getLogoUrlAttribute()
    {
        // 1. Si el usuario subió un logo, usarlo
        if ($this->logo && Storage::disk('public')->exists($this->logo)) {
            return Storage::url($this->logo);
        }

        // 2. Siempre usar la imagen por defecto
        return asset('Images/empresa-logo.jpg');
    }
}