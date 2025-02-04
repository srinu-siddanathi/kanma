<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'branch_id',
        'order_type',
        'status',
        'total_amount',
        'delivery_fee',
        'wallet_amount_used',
        'delivery_address',
        'delivery_latitude',
        'delivery_longitude',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'wallet_amount_used' => 'decimal:2',
        'delivery_latitude' => 'decimal:8',
        'delivery_longitude' => 'decimal:8',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function updateTotalAmount()
    {
        $this->total_amount = $this->items()->sum(\DB::raw('price * quantity'));
        $this->save();
    }
} 