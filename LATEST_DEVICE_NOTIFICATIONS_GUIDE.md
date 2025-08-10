# Latest Device Notification System

## Overview

The notification system has been updated to send push notifications only to the user's **latest device token** instead of all registered devices. This approach:

- **Reduces notification spam** - Users won't receive the same notification on multiple devices
- **Improves performance** - Fewer API calls to Firebase
- **Saves costs** - Reduced Firebase usage
- **Better user experience** - Notifications appear only on the most recently used device

## How It Works

### Device Token Selection Logic

The system selects the latest device token using this priority order:

1. **Most recently used device** (`last_used_at` timestamp, descending)
2. **Most recently registered device** (`created_at` timestamp, descending)

```php
$latestDeviceToken = $user->activeDeviceTokens()
    ->orderBy('last_used_at', 'desc')
    ->orderBy('created_at', 'desc')
    ->first();
```

### Updated Methods

All notification methods now use `sendToUserLatestDevice()` instead of `sendToUser()`:

| Method | Description |
|--------|-------------|
| `sendOrderStatusUpdate()` | Order status changes |
| `sendPaymentSuccess()` | Successful payments |
| `sendPaymentFailure()` | Failed payments |
| `sendDeliveryUpdate()` | Delivery status updates |
| `sendPromotionalNotification()` | Promotional messages |
| `sendWalletUpdate()` | Wallet transactions |
| `sendCouponNotification()` | New coupons |
| `sendGeneralNotification()` | General notifications |
| `sendToMultipleUsers()` | Bulk notifications |

## Implementation Details

### Firebase Service Updates

The `FirebaseNotificationService` now includes:

```php
public function sendToUserLatestDevice(int $userId, string $title, string $body, array $data = []): bool
{
    // Get the latest active device token
    $latestDeviceToken = $user->activeDeviceTokens()
        ->orderBy('last_used_at', 'desc')
        ->orderBy('created_at', 'desc')
        ->first();

    // Send notification to that device only
    return $this->sendToDevice($latestDeviceToken->device_token, $title, $body, $data);
}
```

### Notification Helper Updates

All `NotificationHelper` methods now use the latest device approach:

```php
// Before (sent to all devices)
return self::getFirebaseService()->sendToUser($userId, $title, $message, $data);

// After (sends to latest device only)
return self::getFirebaseService()->sendToUserLatestDevice($userId, $title, $message, $data);
```

## Testing

### Test Scripts

1. **`test_with_your_device.php`** - Tests with a specific device token
2. **`test_latest_device_notifications.php`** - Tests all notification types with latest device logic

### Running Tests

```bash
# Test with your specific device token
php test_with_your_device.php

# Test latest device notification system
php test_latest_device_notifications.php
```

### API Testing

You can also test via the API endpoint:

```bash
# Test with user's latest device
curl -X POST http://your-domain/api/notifications/test \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Test Notification",
    "body": "This is a test notification"
  }'

# Test with specific device token
curl -X POST http://your-domain/api/notifications/test \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "device_token": "YOUR_DEVICE_TOKEN",
    "title": "Test Notification",
    "body": "This is a test notification"
  }'
```

## Usage Examples

### Order Status Updates

```php
use App\Helpers\NotificationHelper;

// Send order confirmation
NotificationHelper::sendOrderStatusUpdate($userId, $orderId, 'confirmed');

// Send delivery update
NotificationHelper::sendDeliveryUpdate($userId, $orderId, 'out_for_delivery', 'John Doe');
```

### Payment Notifications

```php
// Payment success
NotificationHelper::sendPaymentSuccess($userId, $orderId, 299.99);

// Payment failure
NotificationHelper::sendPaymentFailure($userId, $orderId, 'Insufficient funds');
```

### Promotional Notifications

```php
// Send promotional message
NotificationHelper::sendPromotionalNotification(
    $userId, 
    'Special Offer!', 
    'Get 20% off on your next order!'
);
```

### Wallet Updates

```php
// Credit wallet
NotificationHelper::sendWalletUpdate($userId, 100.00, 'credit', 'Referral bonus');

// Debit wallet
NotificationHelper::sendWalletUpdate($userId, 50.00, 'debit', 'Order payment');
```

### Bulk Notifications

```php
// Send to multiple users (each gets notification on their latest device)
$userIds = [1, 2, 3, 4, 5];
$results = NotificationHelper::sendToMultipleUsers(
    $userIds,
    'New Feature Available!',
    'Check out our latest features!'
);
```

## Benefits

### For Users
- **No notification spam** - Only one notification per event
- **Better experience** - Notifications appear on the most relevant device
- **Reduced battery drain** - Fewer notifications to process

### For Developers
- **Simplified logic** - No need to manage multiple device tokens
- **Better performance** - Fewer API calls
- **Cost effective** - Reduced Firebase usage

### For Business
- **Lower costs** - Fewer Firebase API calls
- **Better engagement** - Users are less likely to disable notifications
- **Improved analytics** - Clearer notification delivery metrics

## Migration Notes

### What Changed
- All notification methods now send to latest device only
- No breaking changes to method signatures
- Existing code continues to work without modification

### What Stays the Same
- Method names and parameters remain unchanged
- Return values and error handling remain the same
- Firebase configuration and setup unchanged

## Troubleshooting

### Common Issues

1. **No notifications received**
   - Check if user has active device tokens
   - Verify Firebase configuration
   - Check Laravel logs for errors

2. **Notifications sent to wrong device**
   - Verify `last_used_at` timestamps are updated correctly
   - Check device token registration logic

3. **Firebase errors**
   - Verify service account credentials
   - Check project ID configuration
   - Ensure device tokens are valid

### Debugging

Check the Laravel logs for detailed information:

```bash
tail -f storage/logs/laravel.log
```

Look for entries like:
- `Sending notification to user's latest device`
- `FCM v1 notification sent successfully`
- `No active device tokens found for user`

## Future Enhancements

### Potential Improvements
1. **Device preference settings** - Allow users to choose preferred device
2. **Notification scheduling** - Send notifications at optimal times
3. **Smart device selection** - Consider device type and usage patterns
4. **Fallback mechanisms** - Send to secondary device if primary fails

### Monitoring
- Track notification delivery rates
- Monitor device token validity
- Analyze user engagement patterns
- Optimize notification timing

## Support

For issues or questions:
1. Check the Laravel logs for error details
2. Verify Firebase configuration
3. Test with the provided test scripts
4. Review this documentation

The latest device notification system provides a more efficient and user-friendly approach to push notifications while maintaining all existing functionality. 