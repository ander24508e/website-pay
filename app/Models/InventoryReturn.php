<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryReturn extends Model
{
    protected $fillable = [
        'empresa_id',
        'inventory_location_id',
        'supplier_id',
        'user_id',
        'type',
        'reference',
        'status',
        'notes',
    ];

    public function location()
    {
        return $this->belongsTo(InventoryLocation::class, 'inventory_location_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(InventoryReturnItem::class);
    }
}
