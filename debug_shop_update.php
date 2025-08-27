<?php

/**
 * Debug script for shop update issue in production
 * Place this file in your public directory and access it via browser
 * This will help identify what's causing the shop update to fail
 */

// Include Laravel bootstrap
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Shop;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

echo "<h1>Shop Update Debug Information</h1>";

try {
    // Check if we can connect to the database
    echo "<h2>Database Connection Test</h2>";
    DB::connection()->getPdo();
    echo "<p style='color: green;'>✓ Database connection successful</p>";
    
    // Check if shops table exists and has data
    echo "<h2>Shops Table Check</h2>";
    $shopCount = Shop::count();
    echo "<p>Total shops in database: {$shopCount}</p>";
    
    if ($shopCount > 0) {
        $firstShop = Shop::first();
        echo "<p>First shop ID: {$firstShop->id}</p>";
        echo "<p>First shop name: {$firstShop->name}</p>";
        echo "<p>First shop working_hours: " . json_encode($firstShop->working_hours) . "</p>";
    }
    
    // Check if the working_hours column exists and its type
    echo "<h2>Database Schema Check</h2>";
    $columns = DB::select("DESCRIBE shops");
    foreach ($columns as $column) {
        if ($column->Field === 'working_hours') {
            echo "<p>working_hours column type: {$column->Type}</p>";
            echo "<p>working_hours nullable: {$column->Null}</p>";
        }
    }
    
    // Test shop update functionality
    echo "<h2>Shop Update Test</h2>";
    if ($shopCount > 0) {
        $testShop = Shop::first();
        $originalName = $testShop->name;
        
        try {
            $testShop->update(['name' => 'Test Update ' . time()]);
            echo "<p style='color: green;'>✓ Shop update successful</p>";
            
            // Revert the change
            $testShop->update(['name' => $originalName]);
            echo "<p style='color: green;'>✓ Shop update reverted</p>";
            
        } catch (Exception $e) {
            echo "<p style='color: red;'>✗ Shop update failed: " . $e->getMessage() . "</p>";
        }
    }
    
    // Check Laravel logs
    echo "<h2>Recent Laravel Logs</h2>";
    $logFile = storage_path('logs/laravel.log');
    if (file_exists($logFile)) {
        $logs = file_get_contents($logFile);
        $lines = explode("\n", $logs);
        $recentLines = array_slice($lines, -20); // Last 20 lines
        echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 300px; overflow-y: auto;'>";
        foreach ($recentLines as $line) {
            if (strpos($line, 'Shop update') !== false) {
                echo htmlspecialchars($line) . "\n";
            }
        }
        echo "</pre>";
    } else {
        echo "<p>No log file found at: {$logFile}</p>";
    }
    
    // Check file permissions
    echo "<h2>File Permissions Check</h2>";
    $storagePath = storage_path();
    $bootstrapPath = base_path('bootstrap/cache');
    
    echo "<p>Storage directory writable: " . (is_writable($storagePath) ? 'Yes' : 'No') . "</p>";
    echo "<p>Bootstrap cache writable: " . (is_writable($bootstrapPath) ? 'Yes' : 'No') . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<p><strong>Instructions:</strong></p>";
echo "<ol>";
echo "<li>Run this script in your production environment</li>";
echo "<li>Check the output for any errors or issues</li>";
echo "<li>Look for any database connection or permission issues</li>";
echo "<li>Check the Laravel logs for shop update related errors</li>";
echo "<li>After fixing any issues, delete this file for security</li>";
echo "</ol>"; 