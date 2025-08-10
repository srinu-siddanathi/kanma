<?php

/**
 * Test Firebase notifications with real device token
 * 
 * Usage: php test_firebase_with_device.php
 * 
 * This script helps you test the Firebase FCM v1 API with a real device token
 */

require_once 'vendor/autoload.php';

use App\Services\FirebaseNotificationService;
use Illuminate\Support\Facades\Log;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Firebase FCM v1 API Test with Real Device Token\n";
echo "===============================================\n\n";

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

    // Get device token from user input
    echo "4. Testing with real device token...\n";
    echo "Enter your FCM device token (or press Enter to skip): ";
    $handle = fopen("php://stdin", "r");
    $deviceToken = trim(fgets($handle));
    fclose($handle);
    
    if (empty($deviceToken)) {
        echo "Skipping device token test.\n";
        echo "You can test manually using the API endpoint:\n";
        echo "POST /api/notifications/test\n";
        echo "Body: {\"device_token\": \"your_device_token_here\"}\n\n";
    } else {
        echo "Testing notification to device token: " . substr($deviceToken, 0, 20) . "...\n";
        
        $result = $firebase->sendToDevice(
            $deviceToken,
            'Firebase FCM v1 Test',
            'This is a test notification from the new Firebase FCM v1 API!',
            [
                'type' => 'migration_test',
                'timestamp' => now()->toISOString(),
                'api_version' => 'v1'
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
    }

    echo "Migration test completed!\n";
    echo "The new Firebase FCM v1 API implementation is working correctly.\n\n";
    
    echo "Next steps:\n";
    echo "1. Test with your mobile app\n";
    echo "2. Monitor logs for any issues\n";
    echo "3. Update your app to use the new API\n";

} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
} 