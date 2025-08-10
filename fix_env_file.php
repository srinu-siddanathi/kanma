<?php

/**
 * Fix .env file formatting for Firebase credentials
 * This script will help format the JSON content properly
 */

echo "Fixing .env file formatting...\n\n";

// Read the current .env file
if (!file_exists('.env')) {
    echo "Error: .env file not found!\n";
    exit(1);
}

$envContent = file_get_contents('.env');
$lines = explode("\n", $envContent);

$newLines = [];
$inFirebaseJson = false;
$firebaseJsonContent = '';

foreach ($lines as $line) {
    $line = trim($line);
    
    // Skip empty lines
    if (empty($line)) {
        $newLines[] = '';
        continue;
    }
    
    // Check if this is the start of FIREBASE_SERVICE_ACCOUNT_JSON
    if (strpos($line, 'FIREBASE_SERVICE_ACCOUNT_JSON=') === 0) {
        $inFirebaseJson = true;
        $firebaseJsonContent = substr($line, strlen('FIREBASE_SERVICE_ACCOUNT_JSON='));
        continue;
    }
    
    // If we're in the middle of the JSON, collect the content
    if ($inFirebaseJson) {
        // Check if this line contains the end of JSON (closing brace)
        if (strpos($line, '}') !== false) {
            $firebaseJsonContent .= $line;
            $inFirebaseJson = false;
            
            // Add the complete JSON line
            $newLines[] = 'FIREBASE_SERVICE_ACCOUNT_JSON=' . $firebaseJsonContent;
            $firebaseJsonContent = '';
        } else {
            $firebaseJsonContent .= $line;
        }
        continue;
    }
    
    // Regular line, add it as is
    $newLines[] = $line;
}

// Write the fixed content back to .env
$fixedContent = implode("\n", $newLines);
file_put_contents('.env', $fixedContent);

echo "✓ .env file has been fixed!\n";
echo "✓ JSON content is now on a single line\n\n";

echo "Now testing the configuration...\n\n";

// Test if the .env file is valid now
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    echo "✓ .env file is now valid!\n";
    
    // Check if Firebase variables are set
    $projectId = $_ENV['FIREBASE_PROJECT_ID'] ?? null;
    $serviceAccountJson = $_ENV['FIREBASE_SERVICE_ACCOUNT_JSON'] ?? null;
    
    if ($projectId) {
        echo "✓ FIREBASE_PROJECT_ID is set: {$projectId}\n";
    } else {
        echo "✗ FIREBASE_PROJECT_ID is not set\n";
    }
    
    if ($serviceAccountJson) {
        echo "✓ FIREBASE_SERVICE_ACCOUNT_JSON is set\n";
        
        // Validate JSON
        $jsonData = json_decode($serviceAccountJson, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            echo "✓ JSON is valid\n";
        } else {
            echo "✗ JSON is invalid: " . json_last_error_msg() . "\n";
        }
    } else {
        echo "✗ FIREBASE_SERVICE_ACCOUNT_JSON is not set\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\nYou can now run: php test_firebase_migration.php\n"; 