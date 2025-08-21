<?php

require_once 'vendor/autoload.php';

use App\Models\Order;
use App\Services\OrderPlacementEmailService;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing COD Order Placement Email Service...\n";

try {
    // Find a COD order for testing
    $codOrder = Order::where('payment_method', 'cod')
        ->with(['user', 'branch.user', 'items.product'])
        ->first();

    if (!$codOrder) {
        echo "No COD orders found in the database. Please create a COD order first.\n";
        exit(1);
    }

    echo "Found COD order #{$codOrder->id}\n";
    echo "Customer: {$codOrder->user->name} ({$codOrder->user->email})\n";
    echo "Branch: {$codOrder->branch->name}\n";
    echo "Branch Manager: {$codOrder->branch->user->name} ({$codOrder->branch->user->email})\n";
    echo "Total Amount: ₹{$codOrder->total_amount}\n";
    echo "Status: {$codOrder->status}\n\n";

    // Test the email service
    echo "Sending COD order placement emails...\n";
    OrderPlacementEmailService::sendCodOrderPlacementEmails($codOrder);
    
    echo "✅ COD order placement emails sent successfully!\n";
    echo "Check the logs for detailed information about the email sending process.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} 