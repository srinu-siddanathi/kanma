<?php

/**
 * Manual fix for .env file
 * Run this script to fix the Firebase JSON formatting issue
 */

echo "Manual .env file fix for Firebase credentials\n";
echo "=============================================\n\n";

// Check if .env exists
if (!file_exists('.env')) {
    echo "Error: .env file not found!\n";
    exit(1);
}

echo "Current .env file content preview:\n";
echo "----------------------------------\n";

$envContent = file_get_contents('.env');
$lines = explode("\n", $envContent);

$firebaseLines = [];
$otherLines = [];

foreach ($lines as $line) {
    if (strpos($line, 'FIREBASE_') === 0) {
        $firebaseLines[] = $line;
    } else {
        $otherLines[] = $line;
    }
}

echo "Firebase-related lines found:\n";
foreach ($firebaseLines as $line) {
    echo substr($line, 0, 50) . "...\n";
}

echo "\nThe issue is that your JSON content is split across multiple lines.\n";
echo "You need to manually fix this in your .env file.\n\n";

echo "Steps to fix:\n";
echo "1. Open your .env file in a text editor\n";
echo "2. Find the line starting with FIREBASE_SERVICE_ACCOUNT_JSON=\n";
echo "3. Make sure the entire JSON is on ONE LINE\n";
echo "4. Save the file\n\n";

echo "Example of CORRECT format:\n";
echo "FIREBASE_SERVICE_ACCOUNT_JSON={\"type\":\"service_account\",\"project_id\":\"kanmas-95a40\",...}\n\n";

echo "Example of INCORRECT format:\n";
echo "FIREBASE_SERVICE_ACCOUNT_JSON={\"type\":\"service_account\",\n";
echo "\"project_id\":\"kanmas-95a40\",\n";
echo "...}\n\n";

echo "After fixing, run: php test_firebase_migration.php\n"; 