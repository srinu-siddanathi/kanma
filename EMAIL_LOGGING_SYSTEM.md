# Email Logging System

## Overview
This system provides comprehensive email logging functionality to track all email communications in the KANMA application. It logs emails before sending, tracks their status, and provides tools for monitoring and managing email delivery.

## Features

### ✅ Complete Email Tracking
- Logs all emails before sending
- Tracks email status (pending, sent, failed, bounced)
- Stores recipient information and email content
- Records timestamps for sent, delivered, and opened events

### ✅ Email Types Supported
- Order placement emails (COD orders)
- Order confirmation emails
- Password reset emails
- Email verification emails
- Newsletter emails
- Contact form emails
- Subscription emails
- Refund emails

### ✅ Recipient Types
- Customer
- Branch Manager
- Admin
- Shop Owner
- Delivery Boy

### ✅ Admin Management
- View all email logs with filtering
- Email statistics and analytics
- Retry failed emails
- Export email logs to CSV
- Cleanup old email logs

### ✅ Console Commands
- Retry failed emails
- Cleanup old email logs
- Dry-run mode for testing

## Database Schema

### `email_logs` Table
```sql
CREATE TABLE email_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email_type VARCHAR(255) NOT NULL,
    recipient_type VARCHAR(255) NOT NULL,
    recipient_email VARCHAR(255) NOT NULL,
    recipient_name VARCHAR(255) NULL,
    subject VARCHAR(255) NOT NULL,
    content TEXT NULL,
    metadata JSON NULL,
    status ENUM('pending', 'sent', 'failed', 'bounced') DEFAULT 'pending',
    error_message TEXT NULL,
    sent_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    opened_at TIMESTAMP NULL,
    message_id VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_email_type_status (email_type, status),
    INDEX idx_recipient_email (recipient_email),
    INDEX idx_sent_at (sent_at),
    INDEX idx_recipient_type (recipient_type)
);
```

## Files Created

### Models
- `app/Models/EmailLog.php` - Email log model with relationships and scopes

### Services
- `app/Services/EmailLogService.php` - Service for email logging operations

### Controllers
- `app/Http/Controllers/Admin/EmailLogController.php` - Admin interface for email logs

### Commands
- `app/Console/Commands/RetryFailedEmails.php` - Retry failed emails
- `app/Console/Commands/CleanupEmailLogs.php` - Cleanup old email logs

### Database
- `database/migrations/2024_01_15_000000_create_email_logs_table.php` - Migration

## Usage

### 1. Logging Emails

The system automatically logs emails when using the updated email services:

```php
// Order placement emails
\App\Services\OrderPlacementEmailService::sendOrderPlacementEmails($order);

// Order confirmation emails
\App\Services\OrderEmailService::sendOrderConfirmationEmails($order);
```

### 2. Manual Email Logging

```php
use App\Services\EmailLogService;

// Log any email
$emailLog = EmailLogService::logEmail(
    'custom_type',
    'customer',
    'user@example.com',
    'Email Subject',
    'User Name',
    'Email content...',
    ['order_id' => 123]
);

// Mark as sent
EmailLogService::markAsSent($emailLog, 'message_id_123');

// Mark as failed
EmailLogService::markAsFailed($emailLog, 'SMTP error: Connection timeout');
```

### 3. Admin Interface

Access email logs at: `/admin/email-logs`

Features:
- Filter by email type, recipient type, status, date range
- View email details and content
- Retry failed emails
- Export to CSV
- View statistics

### 4. Console Commands

```bash
# Retry failed emails
php artisan emails:retry-failed --limit=50

# Dry run to see what would be retried
php artisan emails:retry-failed --dry-run

# Cleanup old email logs (keep last 90 days)
php artisan emails:cleanup --days=90

# Dry run to see what would be deleted
php artisan emails:cleanup --dry-run
```

### 5. Email Statistics

```php
use App\Services\EmailLogService;

// Get overall statistics
$stats = EmailLogService::getEmailStatistics();

// Get statistics by type
$statsByType = EmailLogService::getEmailStatisticsByType();

// Get statistics for date range
$stats = EmailLogService::getEmailStatistics('2024-01-01', '2024-01-31');
```

## Email Status Flow

1. **Pending** - Email logged, ready to send
2. **Sent** - Email successfully sent to provider
3. **Delivered** - Email delivered to recipient (if tracking available)
4. **Opened** - Email opened by recipient (if tracking available)
5. **Failed** - Email failed to send
6. **Bounced** - Email bounced back

## Metadata Storage

The `metadata` field stores additional context for each email:

```json
{
    "order_id": 123,
    "user_id": 456,
    "order_type": "placement",
    "payment_method": "cod",
    "branch_id": 789
}
```

## Integration with Existing Services

### Updated Services
- `OrderPlacementEmailService` - Now logs emails before sending
- `OrderEmailService` - Now logs emails before sending

### Error Handling
- Failed emails are automatically logged with error details
- Retry mechanism available for failed emails
- Comprehensive error tracking and reporting

## Monitoring and Alerts

### Email Statistics Dashboard
- Total emails sent
- Success rate
- Failed email count
- Email type breakdown
- Daily email volume

### Failed Email Alerts
- Monitor failed email count
- Set up alerts for high failure rates
- Track email delivery issues

## Performance Considerations

### Indexes
- Optimized indexes for common queries
- Composite indexes for filtering

### Cleanup
- Automatic cleanup of old logs (configurable)
- Keeps pending emails for retry
- Configurable retention period

### Storage
- Email content stored for debugging
- Metadata for context
- Efficient JSON storage for flexible data

## Security

### Data Protection
- Email addresses stored securely
- Content encrypted in transit
- Access control for admin interface

### Privacy
- GDPR compliant logging
- Configurable data retention
- User consent tracking

## Future Enhancements

### Planned Features
- Email delivery tracking (webhooks)
- Email open tracking (pixel tracking)
- Click tracking for links
- Email templates versioning
- A/B testing for email content
- Email scheduling
- Bulk email campaigns

### Integration Possibilities
- Email service provider webhooks
- Analytics integration
- Customer support integration
- Marketing automation

## Troubleshooting

### Common Issues

1. **Emails not being logged**
   - Check if EmailLogService is properly imported
   - Verify database migration is run
   - Check for exceptions in logs

2. **Failed emails not retrying**
   - Verify email status is 'failed'
   - Check error message for details
   - Ensure retry command is scheduled

3. **Performance issues**
   - Check database indexes
   - Monitor query performance
   - Consider cleanup old logs

### Debug Commands

```bash
# Check email log status
php artisan tinker
>>> App\Models\EmailLog::count()

# Check failed emails
>>> App\Models\EmailLog::where('status', 'failed')->count()

# Check recent emails
>>> App\Models\EmailLog::latest()->take(10)->get()
```

## Configuration

### Environment Variables
```env
# Email logging configuration
EMAIL_LOGGING_ENABLED=true
EMAIL_LOG_RETENTION_DAYS=90
EMAIL_RETRY_ATTEMPTS=3
```

### Scheduling Commands
Add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Retry failed emails every hour
    $schedule->command('emails:retry-failed')->hourly();
    
    // Cleanup old logs daily
    $schedule->command('emails:cleanup')->daily();
}
```

This comprehensive email logging system provides full visibility into email communications, helps with debugging delivery issues, and ensures reliable email delivery for the KANMA application. 