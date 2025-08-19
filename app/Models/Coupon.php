<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'minimum_order_amount',
        'maximum_discount',
        'usage_limit',
        'used_count',
        'per_user_limit',
        'valid_from',
        'valid_until',
        'is_active',
        'is_first_time_only',
        'applicable_categories',
        'excluded_categories',
        'applicable_products',
        'excluded_products',
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_active' => 'boolean',
        'is_first_time_only' => 'boolean',
        'applicable_categories' => 'array',
        'excluded_categories' => 'array',
        'applicable_products' => 'array',
        'excluded_products' => 'array',
        'value' => 'decimal:2',
        'minimum_order_amount' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
    ];

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('valid_from', '<=', now())
                    ->where('valid_until', '>=', now());
    }

    public function scopeValid($query)
    {
        return $query->where(function($q) {
            $q->whereNull('usage_limit')
              ->orWhere('used_count', '<', $q->raw('usage_limit'));
        });
    }

    public function isExpired(): bool
    {
        return now()->gt($this->valid_until);
    }

    public function isNotStarted(): bool
    {
        return now()->lt($this->valid_from);
    }

    public function getUsageCountForUser(User $user): int
    {
        return $this->usages()
            ->where('user_id', $user->id)
            ->where('status', CouponUsage::STATUS_CONFIRMED)
            ->count();
    }

    public function canBeUsedByUser(User $user): bool
    {
        // Check if user has reached per-user limit (only confirmed usages)
        if ($this->getUsageCountForUser($user) >= $this->per_user_limit) {
            return false;
        }

        // Check if it's first time only and user has previous orders
        if ($this->is_first_time_only && $user->orders()->exists()) {
            return false;
        }

        return true;
    }

    /**
     * Get total confirmed usage count
     */
    public function getConfirmedUsageCount(): int
    {
        return $this->usages()->confirmed()->count();
    }

    /**
     * Check if usage limit is reached (only counting confirmed usages)
     */
    public function isUsageLimitReached(): bool
    {
        return $this->usage_limit && $this->getConfirmedUsageCount() >= $this->usage_limit;
    }

    /**
     * Handle successful payment - confirm coupon usage
     */
    public function confirmUsage(int $usageId): void
    {
        $usage = $this->usages()
            ->where('id', $usageId)
            ->where('status', CouponUsage::STATUS_PENDING)
            ->first();

        if ($usage) {
            $usage->markAsConfirmed();
            $this->increment('used_count');
        }
    }

    /**
     * Handle failed payment - mark coupon usage as failed
     */
    public function failUsage(int $usageId): void
    {
        $usage = $this->usages()
            ->where('id', $usageId)
            ->where('status', CouponUsage::STATUS_PENDING)
            ->first();

        if ($usage) {
            $usage->markAsFailed();
        }
    }

    /**
     * Get pending usage by usage ID
     */
    public function getPendingUsage(int $usageId): ?CouponUsage
    {
        return $this->usages()
            ->where('id', $usageId)
            ->where('status', CouponUsage::STATUS_PENDING)
            ->first();
    }

    public function calculateDiscount(float $orderAmount): float
    {
        $discount = 0;

        if ($this->type === 'percentage') {
            $discount = ($orderAmount * $this->value) / 100;
            
            // Apply maximum discount limit if set
            if ($this->maximum_discount) {
                $discount = min($discount, $this->maximum_discount);
            }
        } else {
            $discount = $this->value;
        }

        return round($discount, 2);
    }

    public function isValidForOrder(float $orderAmount, User $user = null): bool
    {
        // Check if coupon is active and within validity period
        if (!$this->is_active || $this->isExpired() || $this->isNotStarted()) {
            return false;
        }

        // Check usage limit
        if ($this->isUsageLimitReached()) {
            return false;
        }

        // Check minimum order amount
        if ($orderAmount < $this->minimum_order_amount) {
            return false;
        }

        // Check user-specific conditions
        if ($user && !$this->canBeUsedByUser($user)) {
            return false;
        }

        return true;
    }

    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'Inactive';
        }

        if ($this->isExpired()) {
            return 'Expired';
        }

        if ($this->isNotStarted()) {
            return 'Not Started';
        }

        if ($this->isUsageLimitReached()) {
            return 'Usage Limit Reached';
        }

        return 'Active';
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'Active' => 'green',
            'Inactive' => 'gray',
            'Expired' => 'red',
            'Not Started' => 'yellow',
            'Usage Limit Reached' => 'orange',
            default => 'gray'
        };
    }

    public function incrementUsageCount(): void
    {
        $this->increment('used_count');
    }
} 