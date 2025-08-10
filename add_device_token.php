<?php

/**
 * Add Device Token to Database
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Adding Device Token to Database\n";
echo "==============================\n\n";

// Your device token
$deviceToken = "eQyGv-kIQCKM5D-Oeqg8HC:APA91bEvMwjKd5N2TGjIhK8IEkCHslzPou-32ZyUOLAivt9un-tbTXVvh1bawwv5bdBAHcLWW65Bapa2g_HO3tA35fTZg_BFSwzprkrj6qrjWLnGBC2LCZY";

try {
    // Check if UserDeviceToken model exists
    if (!class_exists('App\Models\UserDeviceToken')) {
        echo "✗ UserDeviceToken model not found.\n";
        exit(1);
    }

    // Check if token already exists
    $existingToken = \App\Models\UserDeviceToken::where('device_token', $deviceToken)->first();
    
    if ($existingToken) {
        echo "✓ Device token already exists in database.\n";
        echo "User ID: {$existingToken->user_id}\n";
        echo "Active: " . ($existingToken->is_active ? 'Yes' : 'No') . "\n";
        
        if (!$existingToken->is_active) {
            echo "\nActivating the device token...\n";
            $existingToken->activate();
            $existingToken->updateLastUsed();
            echo "✓ Device token activated successfully!\n";
        } else {
            echo "✓ Device token is already active.\n";
        }
        
        $token = $existingToken;
    } else {
        echo "Device token not found. Adding to database...\n";
        
        // Get a user to associate with (using the first available user)
        $user = \App\Models\User::first();
        if (!$user) {
            echo "✗ No users found in database. Please create a user first.\n";
            exit(1);
        }
        
        echo "Associating with user: {$user->name} (ID: {$user->id})\n";
        
        // Create new device token
        $token = \App\Models\UserDeviceToken::create([
            'user_id' => $user->id,
            'device_token' => $deviceToken,
            'device_type' => 'android', // or 'ios' based on your device
            'app_version' => '1.0.0',
            'device_model' => 'Test Device',
            'is_active' => true,
            'last_used_at' => now()
        ]);
        
        echo "✓ Device token added successfully!\n";
    }
    
    echo "\nDevice Token Details:\n";
    echo "User ID: {$token->user_id}\n";
    echo "Device Token: " . substr($token->device_token, 0, 30) . "...\n";
    echo "Device Type: {$token->device_type}\n";
    echo "Active: " . ($token->is_active ? 'Yes' : 'No') . "\n";
    echo "Last Used: {$token->last_used_at}\n";
    
    // Test the notification
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "TESTING NOTIFICATION\n";
    echo str_repeat("=", 50) . "\n";
    
    $firebase = app(\App\Services\FirebaseNotificationService::class);
    
    $result = $firebase->sendToUserLatestDevice(
        $token->user_id,
        'Device Token Test',
        'Your device token has been successfully registered!',
        [
            'type' => 'device_registration',
            'timestamp' => now()->toISOString()
        ]
    );
    
    if ($result) {
        echo "✓ Test notification sent successfully!\n";
        echo "Check your device for the notification.\n";
    } else {
        echo "✗ Test notification failed.\n";
        echo "Check the logs for more details.\n";
    }
    
    echo "\nNext steps:\n";
    echo "1. Check your device for the notification\n";
    echo "2. Test with the notification API\n";
    echo "3. Test with your mobile app\n";

} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} 