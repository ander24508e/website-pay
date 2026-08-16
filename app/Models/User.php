<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'telefono',
        'direccion',
        'password',
        'foto_perfil',
        'active',
        'created_by',
        'manager_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'active' => 'boolean',
    ];

    // URL de foto de perfil
    public function getFotoPerfilUrlAttribute()
    {
        return $this->foto_perfil
            ? asset('storage/'.$this->foto_perfil)
            : asset('Images/empresa-logo.jpg');
    }

    // Helpers de rol
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isStaff(): bool
    {
        return $this->hasAnyRole(['admin', 'gerente', 'empleado']);
    }

    public function createdBy()
    {
        return $this->belongsTo(self::class, 'created_by');
    }

    public function manager()
    {
        return $this->belongsTo(self::class, 'manager_id');
    }

    public function managedStaff()
    {
        return $this->hasMany(self::class, 'manager_id');
    }

    public function audits()
    {
        return $this->hasMany(UserAudit::class, 'target_user_id');
    }

    // Relaciones
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function assignedOrders()
    {
        return $this->hasMany(Order::class, 'assigned_to');
    }

    public function attendedSales()
    {
        return $this->hasMany(Sale::class, 'attended_by');
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }
}
