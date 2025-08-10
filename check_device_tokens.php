<?php

/**
 * Check Device Tokens in Database
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking Device Tokens in Database\n";
echo "==================================\n\n";

try {
    // Check if UserDeviceToken model exists
    if (!class_exists('App\Models\UserDeviceToken')) {
        echo "✗ UserDeviceToken model not found.\n";
        exit(1);
    }

    // Get all device tokens
    $tokens = \App\Models\UserDeviceToken::all();
    echo "Total device tokens: " . $tokens->count() . "\n\n";

    if ($tokens->count() > 0) {
        echo "Sample device tokens:\n";
        echo str_repeat("-", 80) . "\n";
        
        $tokens->take(5)->each(function($token) {
            echo "User ID: " . $token->user_id . "\n";
            echo "Device Token: " . substr($token->device_token, 0, 30) . "...\n";
            echo "Active: " . ($token->is_active ? 'Yes' : 'No') . "\n";
            echo "Created: " . $token->created_at . "\n";
            if (isset($token->last_used_at)) {
                echo "Last Used: " . $token->last_used_at . "\n";
            }
            echo str_repeat("-", 40) . "\n";
        });

        // Check active tokens
        $activeTokens = $tokens->where('is_active', true);
        echo "\nActive device tokens: " . $activeTokens->count() . "\n";

        // Check by user
        $usersWithTokens = $tokens->groupBy('user_id');
        echo "Users with device tokens: " . $usersWithTokens->count() . "\n\n";

        echo "Users and their token counts:\n";
        foreach ($usersWithTokens as $userId => $userTokens) {
            $activeCount = $userTokens->where('is_active', true)->count();
            echo "User ID {$userId}: {$userTokens->count()} total, {$activeCount} active\n";
        }

    } else {
        echo "⚠ No device tokens found in database.\n\n";
        echo "This means:\n";
        echo "1. Users haven't registered their device tokens yet\n";
        echo "2. The device token registration API isn't working\n";
        echo "3. The device token table is empty\n\n";
        
        echo "To test notifications, you need to:\n";
        echo "1. Register a device token via your mobile app\n";
        echo "2. Or manually add a device token to the database\n";
        echo "3. Or use the test script with a specific device token\n";
    }

    // Check users
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "USER INFORMATION\n";
    echo str_repeat("=", 50) . "\n";
    
    $users = \App\Models\User::all();
    echo "Total users: " . $users->count() . "\n";
    
    if ($users->count() > 0) {
        echo "\nSample users:\n";
        $users->take(3)->each(function($user) {
            echo "ID: {$user->id}, Name: {$user->name}, Email: {$user->email}\n";
        });
    }

} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} 