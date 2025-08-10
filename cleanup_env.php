<?php

/**
 * Clean up .env file for file-based Firebase approach
 */

echo "Cleaning up .env file for file-based Firebase approach...\n\n";

// Read the current .env file
if (!file_exists('.env')) {
    echo "Error: .env file not found!\n";
    exit(1);
}

$envContent = file_get_contents('.env');
$lines = explode("\n", $envContent);

$newLines = [];
$skipFirebaseJson = false;

foreach ($lines as $line) {
    // Skip the problematic FIREBASE_SERVICE_ACCOUNT_JSON line
    if (strpos($line, 'FIREBASE_SERVICE_ACCOUNT_JSON=') === 0) {
        echo "Removing problematic FIREBASE_SERVICE_ACCOUNT_JSON line...\n";
        continue;
    }
    
    // Keep all other lines
    $newLines[] = $line;
}

// Write the cleaned content back to .env
$cleanedContent = implode("\n", $newLines);
file_put_contents('.env', $cleanedContent);

echo "✓ .env file cleaned successfully!\n\n";

echo "Now you need to add your Firebase project ID to .env:\n";
echo "FIREBASE_PROJECT_ID=kanmas-95a40\n\n";

echo "The system will now use the firebase-credentials.json file in the root directory.\n";
echo "You can now run: php test_firebase_migration.php\n"; 