<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use App\Models\Branch;
use App\Models\Shop;
use App\Models\ProductVariant;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'category_id',
        'subcategory_id',
        'price',
        'base_unit',
        'image_path',
        'is_active',
        'is_verified',
        'status',
        'rejection_reason',
        'shop_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'status' => 'string',
    ];

    protected $appends = ['image_url'];

    const STATUS_PENDING = 'pending';
    const STATUS_VERIFIED = 'verified';
    const STATUS_REJECTED = 'rejected';

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class, 'branch_products')
            ->withPivot('price', 'stock', 'is_active')
            ->withTimestamps();
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->image_path) {
            return Storage::url($this->image_path);
        }
        return null;
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function scopeWithActiveVariants($query)
    {
        return $query->with(['variants' => function($query) {
            $query->where('is_active', true);
        }]);
    }
} 