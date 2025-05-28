<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'unit',
        'quantity',
        'price',
        'stock',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return "{$this->quantity}{$this->unit}";
    }

    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($variant) {
            if ($variant->discount_percentage > 0) {
                $variant->discounted_price = $variant->price * (1 - $variant->discount_percentage / 100);
            }
        });
    }
} 