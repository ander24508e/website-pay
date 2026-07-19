<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryCount extends Model
{
    protected $fillable = [
        'empresa_id',
        'inventory_location_id',
        'user_id',
        'reference',
        'status',
        'notes',
    ];

    public function location()
    {
        return $this->belongsTo(InventoryLocation::class, 'inventory_location_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(InventoryCountItem::class);
    }
}
