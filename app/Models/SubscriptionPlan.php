<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'description',
        'price',
        'validity_days',
        'wallet_addon',
        'free_orders',
        'free_delivery_radius',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'wallet_addon' => 'decimal:2',
        'free_orders' => 'integer',
        'free_delivery_radius' => 'integer',
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
} 