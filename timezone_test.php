<?php

require_once '../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once '../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== TIMEZONE CONFIGURATION TEST ===\n\n";

// Test 1: Check application timezone
echo "1. Application Timezone: " . config('app.timezone') . "\n";

// Test 2: Check PHP default timezone
echo "2. PHP Default Timezone: " . date_default_timezone_get() . "\n";

// Test 3: Check Carbon timezone
echo "3. Carbon Default Timezone: " . \Carbon\Carbon::now()->timezone->getName() . "\n";

// Test 4: Test current time in different formats
$now = now();
echo "4. Current Time (Carbon): " . $now->format('Y-m-d H:i:s T') . "\n";
echo "5. Current Time (PHP): " . date('Y-m-d H:i:s T') . "\n";

// Test 5: Test database connection timezone (if connected)
try {
    $dbTimezone = \DB::select('SELECT @@session.time_zone as timezone')[0]->timezone ?? 'Not set';
    echo "6. Database Session Timezone: " . $dbTimezone . "\n";
} catch (Exception $e) {
    echo "6. Database Session Timezone: Error connecting to database\n";
}

// Test 6: Test timestamp creation
$timestamp = \Carbon\Carbon::now();
echo "7. Sample Timestamp: " . $timestamp->toDateTimeString() . " " . $timestamp->timezone->getName() . "\n";

// Test 7: Test creating a sample record (if database is connected)
try {
    // Create a test record to verify timestamps
    $testUser = new \App\Models\User();
    $testUser->name = 'Timezone Test User';
    $testUser->email = 'timezone-test-' . time() . '@example.com';
    $testUser->password = bcrypt('password');
    $testUser->save();
    
    echo "8. Test Record Created:\n";
    echo "   - Created At: " . $testUser->created_at->format('Y-m-d H:i:s T') . "\n";
    echo "   - Updated At: " . $testUser->updated_at->format('Y-m-d H:i:s T') . "\n";
    
    // Clean up - delete the test record
    $testUser->delete();
    echo "   - Test record deleted\n";
    
} catch (Exception $e) {
    echo "8. Test Record Creation: Error - " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
echo "Expected Results:\n";
echo "- All timezone values should show 'Asia/Kolkata' or '+05:30'\n";
echo "- Current time should show IST (Indian Standard Time)\n";
echo "- Database timestamps should be in Asia/Kolkata timezone\n\n";

// Check if all tests passed
$appTimezone = config('app.timezone');
$phpTimezone = date_default_timezone_get();
$carbonTimezone = \Carbon\Carbon::now()->timezone->getName();

if ($appTimezone === 'Asia/Kolkata' && $phpTimezone === 'Asia/Kolkata' && $carbonTimezone === 'Asia/Kolkata') {
    echo "✅ SUCCESS: All timezone configurations are working correctly!\n";
    echo "Your application is now using Asia/Kolkata (GMT+5:30) timezone globally.\n";
} else {
    echo "❌ ISSUE: Some timezone configurations are not correct.\n";
    echo "Please check the configuration files and clear cache.\n";
}

// Additional detailed tests
echo "\n=== DETAILED TESTS ===\n";

// Test different time formats
echo "Current time in different formats:\n";
echo "- ISO 8601: " . now()->toISOString() . "\n";
echo "- DateTime: " . now()->toDateTimeString() . "\n";
echo "- Date only: " . now()->toDateString() . "\n";
echo "- Time only: " . now()->format('H:i:s T') . "\n";

// Test timezone conversion
echo "\nTimezone conversion tests:\n";
$utcTime = \Carbon\Carbon::now()->utc();
echo "- UTC time: " . $utcTime->format('Y-m-d H:i:s T') . "\n";
$istTime = \Carbon\Carbon::now()->setTimezone('Asia/Kolkata');
echo "- IST time: " . $istTime->format('Y-m-d H:i:s T') . "\n";

// Test time difference
$timeDiff = $utcTime->diffInHours($istTime);
echo "- Time difference (UTC to IST): " . $timeDiff . " hours\n";

echo "\n=== END OF TEST ===\n"; 