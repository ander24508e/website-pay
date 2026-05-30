<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'telefono',
        'direccion',
        'password',
        'foto_perfil',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    // URL de foto de perfil
    public function getFotoPerfilUrlAttribute()
    {
        return $this->foto_perfil
            ? asset('storage/' . $this->foto_perfil)
            : asset('Images/empresa-logo.jpg');
    }

    // Helpers de rol
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    // Relaciones
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
