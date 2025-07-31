# Push Notification API Documentation

## Overview

This document describes the push notification system implementation using Firebase Cloud Messaging (FCM) for the Android application. The system provides a comprehensive solution for sending notifications to users across different scenarios.

## Features

### ✅ **Phase 1 Implementation**
- **Device Token Management**: Register, update, and unregister device tokens
- **Firebase FCM Integration**: Send notifications via Firebase Cloud Messaging
- **Multi-Platform Support**: Android, iOS, and Web push notifications
- **Batch Processing**: Handle multiple device tokens efficiently
- **Error Handling**: Automatic deactivation of failed tokens
- **Common Helper Functions**: Easy-to-use notification methods
- **Login Integration**: Automatic device token registration during login

## Database Schema

### User Device Tokens Table
```sql
CREATE TABLE `user_device_tokens` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` bigint unsigned NOT NULL,
    `device_token` varchar(255) NOT NULL,
    `device_type` enum('android', 'ios', 'web') NOT NULL DEFAULT 'android',
    `app_version` varchar(20) NULL,
    `device_model` varchar(100) NULL,
    `is_active` tinyint(1) NOT NULL DEFAULT '1',
    `last_used_at` timestamp NULL,
    `created_at` timestamp NULL,
    `updated_at` timestamp NULL,
    
    UNIQUE KEY `unique_user_device` (`user_id`, `device_token`),
    KEY `idx_device_token` (`device_token`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_is_active` (`is_active`),
    KEY `idx_device_type` (`device_type`),
    
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);
```

## API Endpoints

### 1. Login with Device Token Registration

**Endpoint:** `POST /api/login`

**Authentication:** Not Required

**Description:** Login with email/password and optionally register device token for push notifications.

**Request Body:**
```json
{
    "email": "user@example.com",
    "password": "password123",
    "device_token": "fcm_device_token_here",
    "device_type": "android",
    "app_version": "1.0.0",
    "device_model": "Samsung Galaxy S21"
}
```

**Success Response (200):**
```json
{
    "token": "auth_token_here",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com"
    },
    "device_token_registered": true
}
```

### 2. OTP Login with Device Token Registration

**Endpoint:** `POST /api/login/otp/verify`

**Authentication:** Not Required

**Description:** Login with OTP and optionally register device token for push notifications.

**Request Body:**
```json
{
    "phone": "9876543210",
    "otp": "123456",
    "device_token": "fcm_device_token_here",
    "device_type": "android",
    "app_version": "1.0.0",
    "device_model": "Samsung Galaxy S21"
}
```

**Success Response (200):**
```json
{
    "status": "success",
    "token": "auth_token_here",
    "user": {
        "id": 1,
        "name": "John Doe",
        "phone": "9876543210"
    },
    "is_new_user": false,
    "device_token_registered": true
}
```

### 3. Registration with Device Token

**Endpoint:** `POST /api/verify-registration`

**Authentication:** Not Required

**Description:** Complete registration with OTP and optionally register device token.

**Request Body:**
```json
{
    "phone": "9876543210",
    "otp": "123456",
    "device_token": "fcm_device_token_here",
    "device_type": "android",
    "app_version": "1.0.0",
    "device_model": "Samsung Galaxy S21"
}
```

**Success Response (200):**
```json
{
    "status": "success",
    "message": "Registration successful",
    "data": {
        "token": "auth_token_here",
        "user": {
            "id": 1,
            "username": "johndoe",
            "phone": "9876543210",
            "referral_code": "ABC123",
            "profile_completed": false,
            "created_at": "2024-01-15T10:30:00Z"
        },
        "device_token_registered": true
    }
}
```

### 4. Logout with Device Token Cleanup

**Endpoint:** `POST /api/logout`

**Authentication:** Required (Bearer Token)

**Description:** Logout and optionally deactivate device token.

**Request Body:**
```json
{
    "device_token": "fcm_device_token_here"
}
```

**Success Response (200):**
```json
{
    "status": "success",
    "message": "Logged out successfully"
}
```

### 5. Register Device Token (Standalone)

**Endpoint:** `POST /api/notifications/register-device`

**Authentication:** Required (Bearer Token)

**Description:** Register a device token for push notifications (standalone endpoint).

**Request Body:**
```json
{
    "device_token": "fcm_device_token_here",
    "device_type": "android",
    "app_version": "1.0.0",
    "device_model": "Samsung Galaxy S21"
}
```

**Validation Rules:**
- `device_token`: Required, max 255 characters
- `device_type`: Optional, must be 'android', 'ios', or 'web' (defaults to 'android')
- `app_version`: Optional, max 20 characters
- `device_model`: Optional, max 100 characters

**Success Response (200):**
```json
{
    "status": "success",
    "message": "Device token registered successfully",
    "data": {
        "device_token": "fcm_device_token_here",
        "device_type": "android"
    }
}
```

### 6. Get Device Tokens

**Endpoint:** `GET /api/notifications/device-tokens`

**Authentication:** Required (Bearer Token)

**Description:** Get all active device tokens for the authenticated user.

**Success Response (200):**
```json
{
    "status": "success",
    "data": {
        "device_tokens": [
            {
                "id": 1,
                "device_token": "fcm_token_1",
                "device_type": "android",
                "app_version": "1.0.0",
                "device_model": "Samsung Galaxy S21",
                "last_used_at": "2024-01-15T10:30:00Z"
            }
        ],
        "total_count": 1
    }
}
```

### 7. Test Notification

**Endpoint:** `POST /api/notifications/test`

**Authentication:** Required (Bearer Token)

**Description:** Send a test notification to the user's registered devices.

**Success Response (200):**
```json
{
    "status": "success",
    "message": "Test notification sent successfully"
}
```

### 8. Update Last Used

**Endpoint:** `POST /api/notifications/update-last-used`

**Authentication:** Required (Bearer Token)

**Description:** Update the last used timestamp for a device token.

**Request Body:**
```json
{
    "device_token": "fcm_device_token_here"
}
```

### 9. Unregister Device Token

**Endpoint:** `DELETE /api/notifications/unregister-device`

**Authentication:** Required (Bearer Token)

**Description:** Unregister a device token (deactivate it).

**Request Body:**
```json
{
    "device_token": "fcm_device_token_here"
}
```

**Success Response (200):**
```json
{
    "status": "success",
    "message": "Device token unregistered successfully"
}
```

## Android Implementation

### 1. Login with Device Token
```kotlin
fun loginWithDeviceToken(email: String, password: String, deviceToken: String) {
    val requestBody = JSONObject().apply {
        put("email", email)
        put("password", password)
        put("device_token", deviceToken)
        put("device_type", "android")
        put("app_version", BuildConfig.VERSION_NAME)
        put("device_model", Build.MODEL)
    }
    
    // Make API call to /api/login
    // Response will include device_token_registered status
}
```

### 2. OTP Login with Device Token
```kotlin
fun loginWithOTPAndDeviceToken(phone: String, otp: String, deviceToken: String) {
    val requestBody = JSONObject().apply {
        put("phone", phone)
        put("otp", otp)
        put("device_token", deviceToken)
        put("device_type", "android")
        put("app_version", BuildConfig.VERSION_NAME)
        put("device_model", Build.MODEL)
    }
    
    // Make API call to /api/login/otp/verify
}
```

### 3. Registration with Device Token
```kotlin
fun registerWithDeviceToken(phone: String, otp: String, deviceToken: String) {
    val requestBody = JSONObject().apply {
        put("phone", phone)
        put("otp", otp)
        put("device_token", deviceToken)
        put("device_type", "android")
        put("app_version", BuildConfig.VERSION_NAME)
        put("device_model", Build.MODEL)
    }
    
    // Make API call to /api/verify-registration
}
```

### 4. Logout with Device Token Cleanup
```kotlin
fun logoutWithDeviceToken(deviceToken: String) {
    val requestBody = JSONObject().apply {
        put("device_token", deviceToken)
    }
    
    // Make API call to /api/logout
}
```

## Common Notification Helper Functions

### Usage Examples

#### 1. Order Status Updates
```php
use App\Helpers\NotificationHelper;

// Send order status update notification
NotificationHelper::sendOrderStatusUpdate($userId, $orderId, 'confirmed');
```

#### 2. Payment Notifications
```php
// Payment success
NotificationHelper::sendPaymentSuccess($userId, $orderId, 250.00);

// Payment failure
NotificationHelper::sendPaymentFailure($userId, $orderId, 'Insufficient funds');
```

#### 3. Delivery Updates
```php
// Delivery status update
NotificationHelper::sendDeliveryUpdate($userId, $orderId, 'out_for_delivery', 'John Doe');
```

#### 4. Wallet Updates
```php
// Wallet credited
NotificationHelper::sendWalletUpdate($userId, 100.00, 'credit', 'Refund for cancelled order');

// Wallet debited
NotificationHelper::sendWalletUpdate($userId, 50.00, 'debit', 'Payment for order #123');
```

#### 5. Promotional Notifications
```php
// Send promotional notification
NotificationHelper::sendPromotionalNotification(
    $userId,
    'Special Offer!',
    'Get 20% off on your next order. Use code SAVE20',
    ['coupon_code' => 'SAVE20']
);
```

#### 6. Coupon Notifications
```php
// Send coupon notification
NotificationHelper::sendCouponNotification(
    $userId,
    'WELCOME10',
    '10% off',
    '2024-12-31'
);
```

#### 7. General Notifications
```php
// Send general notification
NotificationHelper::sendGeneralNotification(
    $userId,
    'Welcome!',
    'Thank you for joining our platform.',
    ['action' => 'open_profile']
);
```

#### 8. Multiple Users
```php
// Send to multiple users
$userIds = [1, 2, 3, 4, 5];
$results = NotificationHelper::sendToMultipleUsers(
    $userIds,
    'New Feature Available!',
    'Check out our latest features in the app.'
);
```

#### 9. Topic Notifications
```php
// Send to topic
NotificationHelper::sendToTopic(
    'all_users',
    'App Update',
    'New version available with exciting features!'
);
```

## Configuration

### Environment Variables
Add these to your `.env` file:

```env
# Firebase Configuration
FIREBASE_SERVER_KEY=your_firebase_server_key_here
FIREBASE_PROJECT_ID=your_firebase_project_id_here
```

### Firebase Setup
1. **Create Firebase Project**: Go to [Firebase Console](https://console.firebase.google.com/)
2. **Add Android App**: Register your Android app with package name
3. **Download google-services.json**: Add to your Android app
4. **Get Server Key**: Go to Project Settings > Cloud Messaging > Server Key

## Integration Examples

### Order Controller Integration
```php
use App\Helpers\NotificationHelper;

public function updateStatus(Order $order, Request $request)
{
    $order->update(['status' => $request->status]);
    
    // Send notification
    NotificationHelper::sendOrderStatusUpdate(
        $order->user_id,
        $order->id,
        $request->status
    );
    
    return response()->json(['status' => 'success']);
}
```

### Payment Controller Integration
```php
public function processPayment(Order $order)
{
    // Process payment logic...
    
    if ($paymentSuccessful) {
        NotificationHelper::sendPaymentSuccess(
            $order->user_id,
            $order->id,
            $order->total_amount
        );
    } else {
        NotificationHelper::sendPaymentFailure(
            $order->user_id,
            $order->id,
            'Payment gateway error'
        );
    }
}
```

## Error Handling

### Common Error Scenarios
1. **Invalid Device Token**: Automatically deactivated
2. **Network Issues**: Retry mechanism in place
3. **Firebase API Errors**: Logged for debugging
4. **User Not Found**: Graceful handling

### Error Response Format
```json
{
    "status": "error",
    "message": "Failed to send notification",
    "error": "Network timeout"
}
```

## Testing

### Test Scenarios
1. **Login with Device Token**: Test device token registration during login
2. **OTP Login with Device Token**: Test device token registration during OTP login
3. **Registration with Device Token**: Test device token registration during registration
4. **Logout with Device Token**: Test device token cleanup during logout
5. **Device Registration**: Register device token
6. **Test Notification**: Send test notification
7. **Order Updates**: Test order status notifications
8. **Payment Notifications**: Test payment success/failure
9. **Multiple Devices**: Test with multiple device tokens

### Test Commands
```bash
# Login with device token
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password123",
    "device_token": "test_token",
    "device_type": "android"
  }'

# OTP login with device token
curl -X POST http://localhost:8000/api/login/otp/verify \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "9876543210",
    "otp": "123456",
    "device_token": "test_token",
    "device_type": "android"
  }'

# Test notification
curl -X POST http://localhost:8000/api/notifications/test \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Security Considerations

1. **Token Validation**: Validate device tokens on registration
2. **User Authorization**: Users can only manage their own tokens
3. **Rate Limiting**: Implement rate limiting on endpoints
4. **Token Encryption**: Store tokens securely
5. **Audit Logging**: Log all notification activities

## Monitoring and Analytics

### Log Events
- Device token registration/unregistration during login/logout
- Notification sending attempts
- Failed notifications
- Token deactivation

### Metrics to Track
- Notification delivery rate
- User engagement
- Device token validity
- Error rates

## Future Enhancements (Phase 2+)

1. **Notification History**: Store and retrieve notification history
2. **Scheduled Notifications**: Send notifications at specific times
3. **Rich Notifications**: Support for images and actions
4. **A/B Testing**: Test different notification content
5. **Analytics Dashboard**: Monitor notification performance
6. **User Preferences**: Allow users to customize notification settings

## Support

For issues or questions regarding the push notification system:
1. Check Firebase Console for delivery status
2. Review application logs for errors
3. Verify device token registration
4. Test with Firebase Console directly 