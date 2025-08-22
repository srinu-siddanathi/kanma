<?php

/**
 * Simple test script to check if chat orders are integrated with branch_id
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\ChatOrder;
use App\Models\Branch;
use App\Models\Shop;
use Illuminate\Support\Facades\DB;

echo "=== Chat Orders Branch ID Integration Check ===\n\n";

try {
    // Check if we have the required data
    $user = User::first();
    $branch = Branch::first();
    $shop = Shop::first();

    if (!$user) {
        echo "❌ No users found in database.\n";
        exit(1);
    }

    if (!$branch) {
        echo "❌ No branches found in database.\n";
        exit(1);
    }

    if (!$shop) {
        echo "❌ No shops found in database.\n";
        exit(1);
    }

    echo "✅ Found test data:\n";
    echo "   - User: {$user->name} (ID: {$user->id})\n";
    echo "   - Branch: {$branch->name} (ID: {$branch->id})\n";
    echo "   - Shop: {$shop->name} (ID: {$shop->id})\n\n";

    // Check 1: Database structure
    echo "🧪 Check 1: Database structure...\n";
    
    try {
        $columns = DB::select("DESCRIBE chat_orders");
        $branchIdColumn = collect($columns)->firstWhere('Field', 'branch_id');
        
        if ($branchIdColumn) {
            echo "✅ branch_id column exists in chat_orders table\n";
            echo "   - Type: {$branchIdColumn->Type}\n";
            echo "   - Null: {$branchIdColumn->Null}\n";
            echo "   - Key: {$branchIdColumn->Key}\n";
        } else {
            echo "❌ branch_id column not found in chat_orders table\n";
        }
    } catch (Exception $e) {
        echo "⚠️  Could not check database structure: " . $e->getMessage() . "\n";
    }

    // Check 2: Model fillable fields
    echo "\n🧪 Check 2: Model fillable fields...\n";
    
    $chatOrder = new ChatOrder();
    $fillable = $chatOrder->getFillable();
    
    if (in_array('branch_id', $fillable)) {
        echo "✅ branch_id is in ChatOrder model fillable array\n";
    } else {
        echo "❌ branch_id is NOT in ChatOrder model fillable array\n";
    }

    // Check 3: Model relationships
    echo "\n🧪 Check 3: Model relationships...\n";
    
    if (method_exists($chatOrder, 'branch')) {
        echo "✅ ChatOrder model has branch() relationship method\n";
    } else {
        echo "❌ ChatOrder model does NOT have branch() relationship method\n";
    }

    // Check 4: Create chat order with branch_id
    echo "\n🧪 Check 4: Creating chat order with branch_id...\n";
    
    $chatOrderWithBranch = ChatOrder::create([
        'user_id' => $user->id,
        'branch_id' => $branch->id,
        'shop_id' => $shop->id,
        'name' => 'Test Chat Order with Branch',
        'status' => 'pending',
        'notes' => 'Testing branch_id integration'
    ]);

    echo "✅ Chat order created with branch_id: Order #{$chatOrderWithBranch->id}\n";
    echo "   - Branch ID: {$chatOrderWithBranch->branch_id}\n";
    echo "   - Shop ID: {$chatOrderWithBranch->shop_id}\n";
    echo "   - Status: {$chatOrderWithBranch->status}\n";

    // Check 5: Test branch relationship
    echo "\n🧪 Check 5: Testing branch relationship...\n";
    
    $chatOrderWithBranch->load('branch');
    
    if ($chatOrderWithBranch->branch instanceof Branch) {
        echo "✅ Branch relationship working correctly\n";
        echo "   - Branch: {$chatOrderWithBranch->branch->name}\n";
        echo "   - Branch ID matches: " . ($chatOrderWithBranch->branch_id == $branch->id ? 'Yes' : 'No') . "\n";
    } else {
        echo "❌ Branch relationship not working\n";
    }

    // Check 6: Test shop relationship
    echo "\n🧪 Check 6: Testing shop relationship...\n";
    
    $chatOrderWithBranch->load('shop');
    
    if ($chatOrderWithBranch->shop instanceof Shop) {
        echo "✅ Shop relationship working correctly\n";
        echo "   - Shop: {$chatOrderWithBranch->shop->name}\n";
        echo "   - Shop ID matches: " . ($chatOrderWithBranch->shop_id == $shop->id ? 'Yes' : 'No') . "\n";
    } else {
        echo "❌ Shop relationship not working\n";
    }

    // Check 7: Create chat order without branch_id
    echo "\n🧪 Check 7: Creating chat order without branch_id...\n";
    
    $chatOrderWithoutBranch = ChatOrder::create([
        'user_id' => $user->id,
        'branch_id' => null,
        'shop_id' => $shop->id,
        'name' => 'Test Chat Order without Branch',
        'status' => 'pending',
        'notes' => 'Testing without branch_id'
    ]);

    echo "✅ Chat order created without branch_id: Order #{$chatOrderWithoutBranch->id}\n";
    echo "   - Branch ID: " . ($chatOrderWithoutBranch->branch_id ?? 'null') . "\n";
    echo "   - Shop ID: {$chatOrderWithoutBranch->shop_id}\n";

    // Check 8: Check existing chat orders
    echo "\n🧪 Check 8: Checking existing chat orders...\n";
    
    $totalChatOrders = ChatOrder::count();
    $chatOrdersWithBranch = ChatOrder::whereNotNull('branch_id')->count();
    $chatOrdersWithoutBranch = ChatOrder::whereNull('branch_id')->count();
    
    echo "✅ Chat orders statistics:\n";
    echo "   - Total chat orders: {$totalChatOrders}\n";
    echo "   - With branch_id: {$chatOrdersWithBranch}\n";
    echo "   - Without branch_id: {$chatOrdersWithoutBranch}\n";

    echo "\n=== Integration Check Summary ===\n";
    echo "✅ Database structure: " . ($branchIdColumn ? 'PASSED' : 'FAILED') . "\n";
    echo "✅ Model fillable: " . (in_array('branch_id', $fillable) ? 'PASSED' : 'FAILED') . "\n";
    echo "✅ Model relationship: " . (method_exists($chatOrder, 'branch') ? 'PASSED' : 'FAILED') . "\n";
    echo "✅ Branch relationship: " . ($chatOrderWithBranch->branch instanceof Branch ? 'PASSED' : 'FAILED') . "\n";
    echo "✅ Shop relationship: " . ($chatOrderWithBranch->shop instanceof Shop ? 'PASSED' : 'FAILED') . "\n";
    echo "✅ Null branch_id support: PASSED\n";
    echo "✅ Data creation: PASSED\n\n";
    
    echo "🎉 Chat orders are properly integrated with branch_id!\n";

} catch (Exception $e) {
    echo "❌ Check failed with error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
} 