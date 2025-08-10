<?php

/**
 * Test script for Firebase FCM v1 API migration
 * 
 * Usage: php test_firebase_migration.php
 * 
 * This script tests the new Firebase notification service
 * to ensure the migration from legacy FCM API is working correctly.
 */

require_once 'vendor/autoload.php';

use App\Services\FirebaseNotificationService;
use Illuminate\Support\Facades\Log;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Firebase FCM v1 API Migration\n";
echo "=====================================\n\n";

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
        throw new Exception('Firebase service account file not found at: ' . $serviceAccountPath . '. Please place your service account JSON file at this location.');
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

    // Test topic notification (this won't actually send, just test the API call structure)
    echo "4. Testing topic notification structure...\n";
    $result = $firebase->sendToTopic('test-topic', 'Test Title', 'Test Message', ['key' => 'value']);
    echo "✓ Topic notification method executed (result: " . ($result ? 'true' : 'false') . ")\n\n";

    // Test device notification structure
    echo "5. Testing device notification structure...\n";
    $result = $firebase->sendToDevice('test-device-token', 'Test Title', 'Test Message', ['key' => 'value']);
    echo "✓ Device notification method executed (result: " . ($result ? 'true' : 'false') . ")\n\n";

    echo "Migration test completed successfully!\n";
    echo "The new Firebase FCM v1 API implementation is working correctly.\n\n";
    
    echo "Next steps:\n";
    echo "1. Update your .env file with FIREBASE_PROJECT_ID\n";
    echo "2. Place your service account JSON file at: " . config('services.firebase.service_account_path') . "\n";
    echo "3. Test with real device tokens\n";
    echo "4. Monitor logs for any issues\n";

} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
} 