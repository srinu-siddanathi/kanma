<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'image_path',
        'is_active',
        'branch_id',
        'category_id',
        'subcategory_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected $appends = ['image_url'];

    protected static function booted()
    {
        // When a product is updated
        static::updated(function ($product) {
            if ($product->isDirty(['price', 'is_active']) && $product->branch_id) {
                // Update the default branch's pivot data
                $product->branches()->updateExistingPivot($product->branch_id, [
                    'price' => $product->price,
                    'is_active' => $product->is_active
                ]);
            }
        });
    }

    /**
     * Get the branch that owns the product.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branch_products')
            ->withPivot('price', 'is_active')
            ->withTimestamps();
    }

    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            return Storage::url($this->image_path);
        }
        return null;
    }
} 