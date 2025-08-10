# Firebase Test API Documentation

## Updated Test Notification Endpoint

The test notification endpoint has been updated to support manual device token testing for the new Firebase FCM v1 API.

### Endpoint: `POST /api/notifications/test`

**Authentication:** Required (Bearer Token)

### Request Body Options:

#### Option 1: Test with User's Registered Devices
```json
{
    "title": "Custom Test Title",
    "body": "Custom test message"
}
```

#### Option 2: Test with Specific Device Token
```json
{
    "device_token": "your_fcm_device_token_here",
    "title": "Custom Test Title",
    "body": "Custom test message"
}
```

### Response Examples:

#### Success Response (200):
```json
{
    "status": "success",
    "message": "Test notification sent successfully to device token",
    "data": {
        "device_token": "your_fcm_device_token_here",
        "title": "Custom Test Title",
        "body": "Custom test message"
    }
}
```

#### Error Response (400):
```json
{
    "status": "error",
    "message": "Failed to send test notification to device token"
}
```

### Testing Commands:

#### 1. Test with User's Registered Devices:
```bash
curl -X POST http://your-domain.com/api/notifications/test \
  -H "Authorization: Bearer YOUR_AUTH_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Test Notification",
    "body": "This is a test message"
  }'
```

#### 2. Test with Specific Device Token:
```bash
curl -X POST http://your-domain.com/api/notifications/test \
  -H "Authorization: Bearer YOUR_AUTH_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "device_token": "your_fcm_device_token_here",
    "title": "Firebase FCM v1 Test",
    "body": "Testing the new Firebase FCM v1 API!"
  }'
```

#### 3. Simple Test (Default Values):
```bash
curl -X POST http://your-domain.com/api/notifications/test \
  -H "Authorization: Bearer YOUR_AUTH_TOKEN" \
  -H "Content-Type: application/json"
```

### How to Get Device Token:

1. **From Android App:**
   ```kotlin
   FirebaseMessaging.getInstance().token.addOnCompleteListener { task ->
       if (task.isSuccessful) {
           val token = task.result
           // Send this token to your server
       }
   }
   ```

2. **From iOS App:**
   ```swift
   Messaging.messaging().token { token, error in
       if let token = token {
           // Send this token to your server
       }
   }
   ```

3. **From Web App:**
   ```javascript
   getMessaging().getToken({ vapidKey: 'your-vapid-key' })
     .then((token) => {
       // Send this token to your server
     });
   ```

### Testing Steps:

1. **Clean up your .env file:**
   ```bash
   php cleanup_env.php
   ```

2. **Add your Firebase project ID to .env:**
   ```env
   FIREBASE_PROJECT_ID=your-project-id
   ```

3. **Test the configuration:**
   ```bash
   php test_firebase_migration.php
   ```

4. **Test with real device token:**
   ```bash
   php test_firebase_with_device.php
   ```

5. **Test via API:**
   ```bash
   curl -X POST http://your-domain.com/api/notifications/test \
     -H "Authorization: Bearer YOUR_AUTH_TOKEN" \
     -H "Content-Type: application/json" \
     -d '{"device_token": "your_device_token"}'
   ```

### Troubleshooting:

1. **Device token not received:**
   - Check if the device is connected to internet
   - Verify the device token is valid
   - Check Firebase Console for delivery status

2. **Authentication errors:**
   - Verify your Firebase service account file
   - Check if the project ID matches
   - Ensure the service account has Firebase Messaging permissions

3. **API errors:**
   - Check the Laravel logs for detailed error messages
   - Verify your authentication token is valid
   - Ensure the endpoint is accessible

### Logs to Monitor:

- Laravel logs: `storage/logs/laravel.log`
- Firebase Console: Check delivery status
- Device logs: Check if notification is received

### Migration Status:

- ✅ Firebase FCM v1 API implementation
- ✅ File-based service account configuration
- ✅ Updated test notification endpoint
- ✅ Manual device token testing support
- ✅ Backward compatibility maintained 