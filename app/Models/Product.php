<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'provider',
        'description',
        'price',
        'image',
        'active',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function activeVariants()
    {
        return $this->hasMany(ProductVariant::class)->where('active', true);
    }

    public function getDisplayPriceAttribute(): float
    {
        $variantPrice = $this->relationLoaded('activeVariants')
            ? $this->activeVariants->min('price')
            : $this->activeVariants()->min('price');

        return (float) ($variantPrice ?? $this->price ?? 0);
    }

    public function orderItems()
    {
        return $this->morphMany(OrderItem::class, 'itemable');
    }
}
