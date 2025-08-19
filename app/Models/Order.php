<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasTimezoneFormatting;

class Order extends Model
{
    use HasTimezoneFormatting;
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
        'delivery_notes',
        'coupon_id',
        'refund_info',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'wallet_amount_used' => 'decimal:2',
        'delivery_latitude' => 'decimal:8',
        'delivery_longitude' => 'decimal:8',
        'refund_info' => 'array',
        'cancelled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the formatted created_at date in user's timezone
     */
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null;
    }

    /**
     * Get the formatted created_at date in UTC
     */
    public function getCreatedAtUtcAttribute()
    {
        return $this->created_at ? $this->created_at->utc()->format('Y-m-d H:i:s') : null;
    }

    /**
     * Get the formatted created_at date in Asia/Kolkata timezone
     */
    public function getCreatedAtAsiaKolkataAttribute()
    {
        return $this->created_at ? $this->created_at->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s') : null;
    }

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

    /**
     * Get the coupon applied to this order.
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
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

    public function address()
    {
        return $this->belongsTo(Address::class);
    }
} 