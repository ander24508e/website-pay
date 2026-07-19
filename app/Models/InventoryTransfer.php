<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryTransfer extends Model
{
    protected $fillable = [
        'empresa_id',
        'from_location_id',
        'to_location_id',
        'user_id',
        'reference',
        'status',
        'notes',
    ];

    public function fromLocation()
    {
        return $this->belongsTo(InventoryLocation::class, 'from_location_id');
    }

    public function toLocation()
    {
        return $this->belongsTo(InventoryLocation::class, 'to_location_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(InventoryTransferItem::class);
    }
}
