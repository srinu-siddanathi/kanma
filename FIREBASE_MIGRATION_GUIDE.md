# Firebase Cloud Messaging Migration Guide

## Overview
This guide helps you migrate from the legacy Firebase Cloud Messaging (FCM) HTTP API to the new Firebase Cloud Messaging HTTP v1 API.

## What Changed

### Legacy FCM API (Deprecated)
- Used Server Key authentication
- Endpoint: `https://fcm.googleapis.com/fcm/send`
- Batch size: 1000 tokens per request
- Simple payload structure

### New FCM HTTP v1 API
- Uses OAuth2 authentication with Service Account
- Endpoint: `https://fcm.googleapis.com/v1/projects/{project_id}/messages:send`
- Batch size: 500 messages per request
- More structured payload format
- Better error handling and reporting

## Migration Steps

### For GoDaddy/Shared Hosting Users

If you're using GoDaddy or similar shared hosting where you don't have access to the `storage` directory, use this simplified approach:

1. **Run the setup script**: `php setup_firebase_godaddy.php`
2. **Follow the on-screen instructions** to configure your credentials
3. **Test the setup**: `php test_firebase_migration.php`

### For Users with Full File Access

### 1. Install Required Dependencies

```bash
composer require google/auth:^1.35
```

### 2. Update Environment Variables

Add these to your `.env` file:

```env
# Firebase Configuration
FIREBASE_PROJECT_ID=your-project-id

# For GoDaddy/Shared Hosting (Recommended)
FIREBASE_SERVICE_ACCOUNT_JSON={"type":"service_account","project_id":"your-project-id",...}

# Alternative: File path (if you have file access)
FIREBASE_SERVICE_ACCOUNT_PATH=firebase-credentials.json

# Legacy (can be removed after migration)
FIREBASE_SERVER_KEY=your-legacy-server-key
```

### 3. Download Service Account Key

1. Go to [Firebase Console](https://console.firebase.google.com/)
2. Select your project
3. Go to Project Settings (gear icon)
4. Go to Service Accounts tab
5. Click "Generate new private key"
6. Download the JSON file

**For GoDaddy/Shared Hosting (Recommended Method):**
- Open the downloaded JSON file
- Copy the entire JSON content
- Paste it as a single line in your `.env` file as `FIREBASE_SERVICE_ACCOUNT_JSON`

**Alternative Method (if you have file access):**
- Save the JSON file as `firebase-credentials.json` in your project root
- Make sure it's added to `.gitignore`

**Important**: Keep this data secure and never commit it to version control!

### 4. Update .gitignore

Add these lines to your `.gitignore`:

```
# Firebase credentials
storage/firebase/service-account.json
firebase-credentials.json
```

### 5. Verify Configuration

The service will automatically:
- Load the service account credentials from environment variable or file
- Generate OAuth2 access tokens
- Cache tokens for reuse
- Handle token refresh when expired

**For GoDaddy users**: The service will first try to load credentials from the `FIREBASE_SERVICE_ACCOUNT_JSON` environment variable, which is the recommended approach for shared hosting environments.

### 6. Test the Migration

You can test the new implementation by sending a test notification:

```php
use App\Services\FirebaseNotificationService;

$firebase = app(FirebaseNotificationService::class);
$result = $firebase->sendToUser(1, 'Test Title', 'Test Message');
```

## Key Differences in Implementation

### Authentication
- **Legacy**: Used `Authorization: key=YOUR_SERVER_KEY`
- **New**: Uses `Authorization: Bearer YOUR_OAUTH_TOKEN`

### Payload Structure
- **Legacy**: Flat structure with `registration_ids`
- **New**: Nested structure with `message` objects

### Error Handling
- **Legacy**: Simple success/failure counts
- **New**: Detailed error codes and messages

### Token Management
- **Legacy**: Manual token validation
- **New**: Automatic token refresh and caching

## Backward Compatibility

The updated service maintains the same public API, so existing code should continue to work without changes:

```php
// These methods work the same way
$firebase->sendToUser($userId, $title, $body, $data);
$firebase->sendToMultipleUsers($userIds, $title, $body, $data);
$firebase->sendToDevice($deviceToken, $title, $body, $data);
$firebase->sendToTopic($topic, $title, $body, $data);
```

## Troubleshooting

### Common Issues

1. **Service Account File Not Found**
   - Ensure the file path is correct in `.env`
   - Check file permissions

2. **Authentication Errors**
   - Verify the service account has the correct permissions
   - Check that the project ID matches your Firebase project

3. **Token Refresh Issues**
   - Ensure the service account has the `firebase.messaging` scope
   - Check network connectivity to Google APIs

### Debug Mode

Enable debug logging by adding this to your `.env`:

```env
LOG_LEVEL=debug
```

## Performance Improvements

The new implementation includes:
- Token caching to reduce API calls
- Automatic token refresh
- Better error handling
- More efficient batch processing

## Security Considerations

1. **Service Account Security**
   - Store the service account file securely
   - Use environment-specific accounts for different environments
   - Rotate keys regularly

2. **Token Management**
   - Tokens are automatically cached and refreshed
   - Failed device tokens are automatically deactivated

## Monitoring

Monitor your FCM usage through:
- Firebase Console Analytics
- Application logs
- Google Cloud Console

## Support

For issues with the new FCM API:
- [Firebase Documentation](https://firebase.google.com/docs/cloud-messaging)
- [Google Cloud Console](https://console.cloud.google.com/)
- [Firebase Support](https://firebase.google.com/support) 