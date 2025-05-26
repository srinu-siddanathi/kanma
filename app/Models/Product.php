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
use App\Models\ProductImage;

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
        'base_price',
        'is_available',
        'is_deal',
        'is_featured',
        'discount'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'status' => 'string',
        'base_price' => 'decimal:2',
        'is_available' => 'boolean',
        'is_deal' => 'boolean',
        'is_featured' => 'boolean',
        'discount' => 'decimal:2'
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

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function scopeWithActiveVariants($query)
    {
        return $query->with(['variants' => function($query) {
            $query->where('is_active', true);
        }]);
    }
} 