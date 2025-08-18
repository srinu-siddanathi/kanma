<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\OrderEmailService;
use App\Models\Order;
use App\Models\User;
use App\Models\Branch;

// This is a test script to verify the order confirmation email service
// Run this with: php test_order_confirmation_email.php

echo "Testing Order Confirmation Email Service...\n";

try {
    // Create test data with static emails for testing
    echo "Creating test order with static emails...\n";
    
    // Find or create test user with static email
    $user = User::where('email', 'srinu.vitam@gmail.com')->first();
    if (!$user) {
        $user = User::create([
            'name' => 'Test Customer',
            'email' => 'srinu.vitam@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'phone' => '1234567890',
        ]);
        echo "Created test customer user\n";
    }
    
    // Find or create test branch with static manager email
    $branchManager = User::where('email', 'srinu.siddanathi@gmail.com')->first();
    if (!$branchManager) {
        $branchManager = User::create([
            'name' => 'Test Branch Manager',
            'email' => 'srinu.siddanathi@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'branch_manager',
            'phone' => '0987654321',
        ]);
        echo "Created test branch manager user\n";
    }
    
    $branch = Branch::where('user_id', $branchManager->id)->first();
    if (!$branch) {
        $branch = Branch::create([
            'user_id' => $branchManager->id,
            'name' => 'Test Branch',
            'address' => 'Test Branch Address',
            'latitude' => 12.9716,
            'longitude' => 77.5946,
            'is_active' => true,
        ]);
        echo "Created test branch\n";
    }
    
    // Create a test order
    $order = Order::create([
        'user_id' => $user->id,
        'branch_id' => $branch->id,
        'status' => 'confirmed',
        'total_amount' => 500.00,
        'delivery_fee' => 50.00,
        'payment_method' => 'cod',
        'payment_status' => 'pending',
        'delivery_address' => 'Test Address, Test City, Test State - 123456',
        'delivery_latitude' => 12.9716,
        'delivery_longitude' => 77.5946,
    ]);
    
    echo "Test order created with ID: {$order->id}\n";
    
    echo "Testing email service with order ID: {$order->id}\n";
    echo "Customer: {$user->name} (srinu.vitam@gmail.com)\n";
    echo "Branch Manager: {$branchManager->name} (srinu.siddanathi@gmail.com)\n";
    
    // Test the email service
    OrderEmailService::sendOrderConfirmationEmails($order);
    
    echo "✅ Order confirmation emails sent successfully!\n";
    echo "Check the logs for more details.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} 