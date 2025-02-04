<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'address',
        'latitude',
        'longitude',
        'contact_number',
        'email',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean',
    ];

    /**
     * Get the manager associated with the branch.
     */
    public function manager(): HasOne
    {
        return $this->hasOne(User::class)->where('role', 'branch_manager');
    }

    /**
     * Get the products for the branch.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the orders for the branch.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
} 