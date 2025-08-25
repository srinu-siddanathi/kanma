<?php

namespace App\Services;

use App\Models\EmailLog;
use Illuminate\Support\Facades\Log;

class EmailLogService
{
    /**
     * Log an email before sending
     */
    public static function logEmail(
        string $emailType,
        string $recipientType,
        string $recipientEmail,
        string $subject,
        ?string $recipientName = null,
        ?string $content = null,
        ?array $metadata = null
    ): EmailLog {
        try {
            return EmailLog::create([
                'email_type' => $emailType,
                'recipient_type' => $recipientType,
                'recipient_email' => $recipientEmail,
                'recipient_name' => $recipientName,
                'subject' => $subject,
                'content' => $content,
                'metadata' => $metadata,
                'status' => EmailLog::STATUS_PENDING,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create email log', [
                'email_type' => $emailType,
                'recipient_email' => $recipientEmail,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Mark email as sent successfully
     */
    public static function markAsSent(EmailLog $emailLog, ?string $messageId = null): void
    {
        try {
            $emailLog->markAsSent($messageId);
            
            Log::info('Email marked as sent', [
                'email_log_id' => $emailLog->id,
                'recipient_email' => $emailLog->recipient_email,
                'email_type' => $emailLog->email_type,
                'message_id' => $messageId,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to mark email as sent', [
                'email_log_id' => $emailLog->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Mark email as failed
     */
    public static function markAsFailed(EmailLog $emailLog, ?string $errorMessage = null): void
    {
        try {
            $emailLog->markAsFailed($errorMessage);
            
            Log::error('Email marked as failed', [
                'email_log_id' => $emailLog->id,
                'recipient_email' => $emailLog->recipient_email,
                'email_type' => $emailLog->email_type,
                'error_message' => $errorMessage,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to mark email as failed', [
                'email_log_id' => $emailLog->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Log order placement email
     */
    public static function logOrderPlacementEmail(
        string $recipientType,
        string $recipientEmail,
        string $recipientName,
        int $orderId,
        string $subject
    ): EmailLog {
        $metadata = [
            'order_id' => $orderId,
            'order_type' => 'placement',
        ];

        return self::logEmail(
            EmailLog::TYPE_ORDER_PLACED,
            $recipientType,
            $recipientEmail,
            $subject,
            $recipientName,
            null,
            $metadata
        );
    }

    /**
     * Log order confirmation email
     */
    public static function logOrderConfirmationEmail(
        string $recipientType,
        string $recipientEmail,
        string $recipientName,
        int $orderId,
        string $subject
    ): EmailLog {
        $metadata = [
            'order_id' => $orderId,
            'order_type' => 'confirmation',
        ];

        return self::logEmail(
            EmailLog::TYPE_ORDER_CONFIRMED,
            $recipientType,
            $recipientEmail,
            $subject,
            $recipientName,
            null,
            $metadata
        );
    }

    /**
     * Log password reset email
     */
    public static function logPasswordResetEmail(
        string $recipientEmail,
        string $recipientName,
        int $userId,
        string $subject
    ): EmailLog {
        $metadata = [
            'user_id' => $userId,
        ];

        return self::logEmail(
            EmailLog::TYPE_PASSWORD_RESET,
            EmailLog::RECIPIENT_CUSTOMER,
            $recipientEmail,
            $subject,
            $recipientName,
            null,
            $metadata
        );
    }

    /**
     * Log verification email
     */
    public static function logVerificationEmail(
        string $recipientEmail,
        string $recipientName,
        int $userId,
        string $subject
    ): EmailLog {
        $metadata = [
            'user_id' => $userId,
        ];

        return self::logEmail(
            EmailLog::TYPE_VERIFICATION,
            EmailLog::RECIPIENT_CUSTOMER,
            $recipientEmail,
            $subject,
            $recipientName,
            null,
            $metadata
        );
    }

    /**
     * Get email statistics
     */
    public static function getEmailStatistics($startDate = null, $endDate = null): array
    {
        $query = EmailLog::query();

        if ($startDate && $endDate) {
            $query->betweenDates($startDate, $endDate);
        }

        $totalEmails = $query->count();
        $sentEmails = $query->clone()->withStatus(EmailLog::STATUS_SENT)->count();
        $failedEmails = $query->clone()->withStatus(EmailLog::STATUS_FAILED)->count();
        $pendingEmails = $query->clone()->withStatus(EmailLog::STATUS_PENDING)->count();

        return [
            'total' => $totalEmails,
            'sent' => $sentEmails,
            'failed' => $failedEmails,
            'pending' => $pendingEmails,
            'success_rate' => $totalEmails > 0 ? round(($sentEmails / $totalEmails) * 100, 2) : 0,
        ];
    }

    /**
     * Get email statistics by type
     */
    public static function getEmailStatisticsByType($startDate = null, $endDate = null): array
    {
        $query = EmailLog::query();

        if ($startDate && $endDate) {
            $query->betweenDates($startDate, $endDate);
        }

        return $query->selectRaw('
                email_type,
                COUNT(*) as total,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as sent,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as failed,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending
            ', [
                EmailLog::STATUS_SENT,
                EmailLog::STATUS_FAILED,
                EmailLog::STATUS_PENDING,
            ])
            ->groupBy('email_type')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->email_type => [
                    'total' => $item->total,
                    'sent' => $item->sent,
                    'failed' => $item->failed,
                    'pending' => $item->pending,
                    'success_rate' => $item->total > 0 ? round(($item->sent / $item->total) * 100, 2) : 0,
                ]];
            })
            ->toArray();
    }

    /**
     * Get failed emails for retry
     */
    public static function getFailedEmailsForRetry(int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return EmailLog::withStatus(EmailLog::STATUS_FAILED)
            ->whereNotNull('error_message')
            ->where('created_at', '>=', now()->subDays(7)) // Only retry emails from last 7 days
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Clean up old email logs
     */
    public static function cleanupOldLogs(int $daysToKeep = 90): int
    {
        $cutoffDate = now()->subDays($daysToKeep);
        
        return EmailLog::where('created_at', '<', $cutoffDate)
            ->where('status', '!=', EmailLog::STATUS_PENDING) // Don't delete pending emails
            ->delete();
    }
} 