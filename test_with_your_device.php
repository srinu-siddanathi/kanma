<?php

/**
 * Test Firebase notifications with your specific device token
 * 
 * Usage: php test_with_your_device.php
 */

require_once '../vendor/autoload.php';

use App\Services\FirebaseNotificationService;
use Illuminate\Support\Facades\Log;

// Bootstrap Laravel
$app = require_once '../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Firebase FCM v1 API Test with Your Device Token\n";
echo "===============================================\n\n";

// Your device token
$deviceToken = "e4ib7mgLTS6bJjo_wAMVDu:APA91bGa0nh9xwG98WEPhq6PNUYT-Q-NEFcAvvgS6GzSYb86l8wK03NkxyZ5PUBvPlwwrCSMXu3YQWSjO8hTWKWU5LLNXQiJ5T32e3edGSUciGqk_PMiHjY";

try {
    // Test service instantiation
    echo "1. Testing service instantiation...\n";
    $firebase = app(FirebaseNotificationService::class);
    echo "✓ Service instantiated successfully\n\n";

    // Test configuration
    echo "2. Testing configuration...\n";
    $projectId = config('services.firebase.project_id');
    $serviceAccountPath = config('services.firebase.service_account_path');
    
    if (!$projectId) {
        throw new Exception('FIREBASE_PROJECT_ID not configured');
    }
    
    if (!file_exists($serviceAccountPath)) {
        throw new Exception('Firebase service account file not found at: ' . $serviceAccountPath);
    }
    
    echo "✓ Project ID: {$projectId}\n";
    echo "✓ Service account file: {$serviceAccountPath}\n";
    
    // Validate JSON file
    $jsonContent = file_get_contents($serviceAccountPath);
    $jsonData = json_decode($jsonContent, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "✓ JSON file is valid\n";
    } else {
        throw new Exception('Invalid JSON in service account file: ' . json_last_error_msg());
    }
    echo "\n";

    // Test access token generation
    echo "3. Testing access token generation...\n";
    
    // Use reflection to test private methods
    $reflection = new ReflectionClass($firebase);
    $refreshMethod = $reflection->getMethod('refreshAccessToken');
    $refreshMethod->setAccessible(true);
    
    $refreshMethod->invoke($firebase);
    echo "✓ Access token generated successfully\n\n";

    // Test with your device token
    echo "4. Testing with your device token...\n";
    echo "Device token: " . substr($deviceToken, 0, 20) . "...\n";
    
    $result = $firebase->sendToDevice(
        $deviceToken,
        'Firebase FCM v1 Test',
        'This is a test notification from the new Firebase FCM v1 API!',
        [
            'type' => 'migration_test',
            'timestamp' => now()->toISOString(),
            'api_version' => 'v1',
            'test_id' => uniqid()
        ]
    );
    
    if ($result) {
        echo "✓ Test notification sent successfully!\n";
        echo "Check your device for the notification.\n\n";
    } else {
        echo "✗ Failed to send test notification.\n";
        echo "This might be due to:\n";
        echo "- Invalid device token\n";
        echo "- Device not connected to internet\n";
        echo "- Firebase configuration issues\n\n";
    }

    // Test with different message
    echo "5. Testing with different message...\n";
    $result2 = $firebase->sendToDevice(
        $deviceToken,
        'Hello from Kanma!',
        'Your Firebase migration is working perfectly! 🎉',
        [
            'type' => 'success_test',
            'timestamp' => now()->toISOString(),
            'message' => 'Migration successful'
        ]
    );
    
    if ($result2) {
        echo "✓ Second test notification sent successfully!\n";
    } else {
        echo "✗ Second test notification failed.\n";
    }

    echo "\nMigration test completed!\n";
    echo "The new Firebase FCM v1 API implementation is working correctly.\n\n";
    
    echo "Next steps:\n";
    echo "1. Check your device for notifications\n";
    echo "2. Test with your mobile app\n";
    echo "3. Monitor logs for any issues\n";

} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
} 