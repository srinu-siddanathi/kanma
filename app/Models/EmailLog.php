<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTimezoneFormatting;

class EmailLog extends Model
{
    use HasFactory, HasTimezoneFormatting;

    protected $fillable = [
        'email_type',
        'recipient_type',
        'recipient_email',
        'recipient_name',
        'subject',
        'content',
        'metadata',
        'status',
        'error_message',
        'sent_at',
        'delivered_at',
        'opened_at',
        'message_id',
    ];

    protected $casts = [
        'metadata' => 'array',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'opened_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Email types
    const TYPE_ORDER_PLACED = 'order_placed';
    const TYPE_ORDER_CONFIRMED = 'order_confirmed';
    const TYPE_PASSWORD_RESET = 'password_reset';
    const TYPE_VERIFICATION = 'verification';
    const TYPE_NEWSLETTER = 'newsletter';
    const TYPE_CONTACT_FORM = 'contact_form';
    const TYPE_SUBSCRIPTION = 'subscription';
    const TYPE_REFUND = 'refund';

    // Recipient types
    const RECIPIENT_CUSTOMER = 'customer';
    const RECIPIENT_BRANCH_MANAGER = 'branch_manager';
    const RECIPIENT_ADMIN = 'admin';
    const RECIPIENT_SHOP_OWNER = 'shop_owner';
    const RECIPIENT_DELIVERY_BOY = 'delivery_boy';

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';
    const STATUS_BOUNCED = 'bounced';

    /**
     * Scope to filter by email type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('email_type', $type);
    }

    /**
     * Scope to filter by recipient type
     */
    public function scopeToRecipientType($query, $recipientType)
    {
        return $query->where('recipient_type', $recipientType);
    }

    /**
     * Scope to filter by status
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by recipient email
     */
    public function scopeToEmail($query, $email)
    {
        return $query->where('recipient_email', $email);
    }

    /**
     * Get email type options for forms
     */
    public static function getEmailTypeOptions()
    {
        return [
            self::TYPE_ORDER_PLACED => 'Order Placed',
            self::TYPE_ORDER_CONFIRMED => 'Order Confirmed',
            self::TYPE_PASSWORD_RESET => 'Password Reset',
            self::TYPE_VERIFICATION => 'Email Verification',
            self::TYPE_NEWSLETTER => 'Newsletter',
            self::TYPE_CONTACT_FORM => 'Contact Form',
            self::TYPE_SUBSCRIPTION => 'Subscription',
            self::TYPE_REFUND => 'Refund',
        ];
    }

    /**
     * Get recipient type options for forms
     */
    public static function getRecipientTypeOptions()
    {
        return [
            self::RECIPIENT_CUSTOMER => 'Customer',
            self::RECIPIENT_BRANCH_MANAGER => 'Branch Manager',
            self::RECIPIENT_ADMIN => 'Admin',
            self::RECIPIENT_SHOP_OWNER => 'Shop Owner',
            self::RECIPIENT_DELIVERY_BOY => 'Delivery Boy',
        ];
    }

    /**
     * Get status options for forms
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_SENT => 'Sent',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_BOUNCED => 'Bounced',
        ];
    }

    /**
     * Mark email as sent
     */
    public function markAsSent($messageId = null)
    {
        $this->update([
            'status' => self::STATUS_SENT,
            'sent_at' => now(),
            'message_id' => $messageId,
        ]);
    }

    /**
     * Mark email as failed
     */
    public function markAsFailed($errorMessage = null)
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Mark email as delivered
     */
    public function markAsDelivered()
    {
        $this->update([
            'delivered_at' => now(),
        ]);
    }

    /**
     * Mark email as opened
     */
    public function markAsOpened()
    {
        $this->update([
            'opened_at' => now(),
        ]);
    }

    /**
     * Get metadata value
     */
    public function getMetadataValue($key, $default = null)
    {
        return $this->metadata[$key] ?? $default;
    }

    /**
     * Set metadata value
     */
    public function setMetadataValue($key, $value)
    {
        $metadata = $this->metadata ?? [];
        $metadata[$key] = $value;
        $this->update(['metadata' => $metadata]);
    }
} 