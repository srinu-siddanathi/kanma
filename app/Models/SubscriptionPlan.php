<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'duration_days',
        'features',
        'is_popular',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'is_popular' => 'boolean',
        'is_active' => 'boolean'
    ];

    const TYPE_KATHA = 'katha';
    const TYPE_O2 = 'o2';

    public function scopeKatha($query)
    {
        return $query->where('type', self::TYPE_KATHA);
    }

    public function scopeO2($query)
    {
        return $query->where('type', self::TYPE_O2);
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
} 