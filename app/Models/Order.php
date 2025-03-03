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
        'shop_id',
        'order_type',
        'status',
        'total_amount',
        'delivery_fee',
        'wallet_amount_used',
        'delivery_address',
        'delivery_latitude',
        'delivery_longitude',
        'notes',
        'customer_id',
        'delivery_boy_id',
        'payment_status',
        'payment_method',
        'delivery_phone',
        'delivery_instructions',
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

    /**
     * Get the branch that owns the order.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function updateTotalAmount()
    {
        $this->total_amount = $this->items()->sum(\DB::raw('price * quantity'));
        $this->save();
    }

    /**
     * Get the customer that owns the order.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the delivery boy assigned to the order.
     */
    public function deliveryBoy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delivery_boy_id');
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
} 