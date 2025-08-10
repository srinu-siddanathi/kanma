<?php

/**
 * Test Latest Device Notification System
 * 
 * This script tests the new notification system that sends notifications
 * only to the user's latest device token.
 */

require_once 'vendor/autoload.php';

use App\Helpers\NotificationHelper;
use App\Models\User;
use Illuminate\Support\Facades\Log;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Latest Device Notification System\n";
echo "========================================\n\n";

try {
    // Test with a specific user (you can change this user ID)
    $userId = 6; // Change this to a valid user ID in your system
    
    echo "1. Testing with user ID: {$userId}\n";
    
    $user = User::find($userId);
    if (!$user) {
        echo "✗ User not found with ID: {$userId}\n";
        echo "Please change the user ID in this script to a valid user.\n";
        exit(1);
    }
    
    echo "✓ User found: {$user->name}\n";
    
    // Check user's device tokens
    $deviceTokens = $user->activeDeviceTokens()->get();
    echo "✓ User has " . $deviceTokens->count() . " active device tokens\n";
    
    if ($deviceTokens->count() > 0) {
        $latestToken = $deviceTokens->sortByDesc('last_used_at')->first();
        echo "✓ Latest device token: " . substr($latestToken->device_token, 0, 20) . "...\n";
        echo "✓ Last used: " . $latestToken->last_used_at . "\n\n";
    } else {
        echo "⚠ No active device tokens found for this user\n\n";
    }

    // Test different types of notifications
    echo "2. Testing Order Status Update Notification\n";
    $result1 = NotificationHelper::sendOrderStatusUpdate($userId, 123, 'confirmed');
    echo $result1 ? "✓ Order status notification sent successfully\n" : "✗ Order status notification failed\n";

    echo "\n3. Testing Payment Success Notification\n";
    $result2 = NotificationHelper::sendPaymentSuccess($userId, 123, 299.99);
    echo $result2 ? "✓ Payment success notification sent successfully\n" : "✗ Payment success notification failed\n";

    echo "\n4. Testing Promotional Notification\n";
    $result3 = NotificationHelper::sendPromotionalNotification($userId, 'Special Offer!', 'Get 20% off on your next order!');
    echo $result3 ? "✓ Promotional notification sent successfully\n" : "✗ Promotional notification failed\n";

    echo "\n5. Testing General Notification\n";
    $result4 = NotificationHelper::sendGeneralNotification($userId, 'Welcome!', 'Thank you for using our app!');
    echo $result4 ? "✓ General notification sent successfully\n" : "✗ General notification failed\n";

    echo "\n6. Testing Wallet Update Notification\n";
    $result5 = NotificationHelper::sendWalletUpdate($userId, 100.00, 'credit', 'Referral bonus');
    echo $result5 ? "✓ Wallet update notification sent successfully\n" : "✗ Wallet update notification failed\n";

    echo "\n7. Testing Coupon Notification\n";
    $result6 = NotificationHelper::sendCouponNotification($userId, 'SAVE20', '20% off', '2024-12-31');
    echo $result6 ? "✓ Coupon notification sent successfully\n" : "✗ Coupon notification failed\n";

    // Summary
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "TEST SUMMARY\n";
    echo str_repeat("=", 50) . "\n";
    
    $tests = [
        'Order Status Update' => $result1,
        'Payment Success' => $result2,
        'Promotional' => $result3,
        'General' => $result4,
        'Wallet Update' => $result5,
        'Coupon' => $result6
    ];
    
    $passed = 0;
    $total = count($tests);
    
    foreach ($tests as $test => $result) {
        $status = $result ? "✓ PASS" : "✗ FAIL";
        echo sprintf("%-20s: %s\n", $test, $status);
        if ($result) $passed++;
    }
    
    echo "\nResults: {$passed}/{$total} tests passed\n";
    
    if ($passed === $total) {
        echo "🎉 All tests passed! The latest device notification system is working perfectly!\n";
    } else {
        echo "⚠ Some tests failed. Check the logs for more details.\n";
    }

    echo "\nNext steps:\n";
    echo "1. Check your device for notifications\n";
    echo "2. Verify that only the latest device receives notifications\n";
    echo "3. Test with your mobile app\n";
    echo "4. Monitor logs for any issues\n";

} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
} 