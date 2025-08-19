<?php

require_once '../vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once '../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;
use Carbon\Carbon;

echo "=== Timezone Debug Information ===\n\n";

// 1. Check current timezone settings
echo "1. Current Timezone Settings:\n";
echo "   - PHP Timezone: " . date_default_timezone_get() . "\n";
echo "   - Laravel App Timezone: " . config('app.timezone') . "\n";
echo "   - Database Timezone: " . config('database.connections.mysql.timezone') . "\n";
echo "   - Current Time (PHP): " . date('Y-m-d H:i:s T') . "\n";
echo "   - Current Time (Carbon): " . now()->format('Y-m-d H:i:s T') . "\n";
echo "   - Current Time (UTC): " . now()->utc()->format('Y-m-d H:i:s T') . "\n";
echo "   - Current Time (Asia/Kolkata): " . now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s T') . "\n\n";

// 2. Check database timezone
echo "2. Database Timezone Check:\n";
try {
    $dbTimezone = \DB::select('SELECT @@global.time_zone, @@session.time_zone')[0];
    echo "   - Global Timezone: " . $dbTimezone->{'@@global.time_zone'} . "\n";
    echo "   - Session Timezone: " . $dbTimezone->{'@@session.time_zone'} . "\n";
} catch (Exception $e) {
    echo "   - Error checking database timezone: " . $e->getMessage() . "\n";
}
echo "\n";

// 3. Check recent orders and their timezone handling
echo "3. Recent Orders Timezone Analysis:\n";
$recentOrders = Order::latest()->take(3)->get();

foreach ($recentOrders as $order) {
    echo "   Order #{$order->id}:\n";
    echo "   - Raw created_at: " . $order->getRawOriginal('created_at') . "\n";
    echo "   - Formatted created_at: " . $order->created_at->format('Y-m-d H:i:s T') . "\n";
    echo "   - UTC: " . $order->getUtcDate('created_at') . "\n";
    echo "   - Asia/Kolkata: " . $order->getAsiaKolkataDate('created_at') . "\n";
    echo "   - API Format: " . $order->getApiDate('created_at') . "\n";
    echo "   - Timestamp: " . $order->created_at->timestamp . "\n";
    echo "\n";
}

// 4. Test timezone conversions
echo "4. Timezone Conversion Test:\n";
$testTime = '2024-01-15 16:42:00';
echo "   - Original Time: {$testTime}\n";

$carbonTime = Carbon::parse($testTime, 'Asia/Kolkata');
echo "   - Parsed as Asia/Kolkata: " . $carbonTime->format('Y-m-d H:i:s T') . "\n";
echo "   - Converted to UTC: " . $carbonTime->utc()->format('Y-m-d H:i:s T') . "\n";
echo "   - Converted back to Asia/Kolkata: " . $carbonTime->utc()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s T') . "\n\n";

// 5. Recommendations
echo "5. Recommendations:\n";
echo "   - Database should store timestamps in UTC\n";
echo "   - Application should convert to local timezone for display\n";
echo "   - API responses should include timezone information\n";
echo "   - Use the HasTimezoneFormatting trait for consistent formatting\n";
echo "   - Set APP_TIMEZONE=Asia/Kolkata in .env\n";
echo "   - Set DB_TIMEZONE=UTC in .env\n";
echo "   - Set API_TIMEZONE=Asia/Kolkata in .env\n\n";

echo "=== End Timezone Debug ===\n"; 