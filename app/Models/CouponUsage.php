<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouponUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'coupon_id',
        'user_id',
        'order_amount',
        'discount_amount',
        'status',
    ];

    protected $casts = [
        'order_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_FAILED = 'failed';

    /**
     * Scope for pending usages
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for confirmed usages
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    /**
     * Scope for failed usages
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Mark usage as confirmed (payment successful)
     */
    public function markAsConfirmed()
    {
        $this->update(['status' => self::STATUS_CONFIRMED]);
    }

    /**
     * Mark usage as failed (payment failed)
     */
    public function markAsFailed()
    {
        $this->update(['status' => self::STATUS_FAILED]);
    }

    /**
     * Check if usage is pending
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if usage is confirmed
     */
    public function isConfirmed()
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    /**
     * Check if usage is failed
     */
    public function isFailed()
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 