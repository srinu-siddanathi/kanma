<?php

/**
 * Firebase Setup Script for File-Based Approach
 * 
 * This script helps you set up Firebase credentials using a JSON file
 * instead of environment variables.
 * 
 * Usage: php setup_firebase_file.php
 */

echo "Firebase Setup for File-Based Approach\n";
echo "=====================================\n\n";

echo "This script will help you set up Firebase credentials using a JSON file.\n\n";

echo "Step 1: Download Service Account Key\n";
echo "-----------------------------------\n";
echo "1. Go to https://console.firebase.google.com/\n";
echo "2. Select your project\n";
echo "3. Go to Project Settings (gear icon)\n";
echo "4. Go to Service Accounts tab\n";
echo "5. Click 'Generate new private key'\n";
echo "6. Download the JSON file\n\n";

echo "Step 2: Place the JSON File\n";
echo "---------------------------\n";
echo "You have several options for where to place the file:\n\n";

echo "Option A: Project Root (Recommended)\n";
echo "- Rename the downloaded file to: firebase-credentials.json\n";
echo "- Place it in your project root directory\n";
echo "- Path: " . __DIR__ . "/firebase-credentials.json\n\n";

echo "Option B: Custom Directory\n";
echo "- Create a directory like: config/firebase/\n";
echo "- Place the file there and update your .env:\n";
echo "  FIREBASE_SERVICE_ACCOUNT_PATH=config/firebase/service-account.json\n\n";

echo "Option C: Public Directory (Less Secure)\n";
echo "- Place in: public/firebase-credentials.json\n";
echo "- Update your .env:\n";
echo "  FIREBASE_SERVICE_ACCOUNT_PATH=public/firebase-credentials.json\n\n";

echo "Step 3: Update Your .env File\n";
echo "-----------------------------\n";
echo "Add these lines to your .env file:\n\n";

echo "FIREBASE_PROJECT_ID=your-project-id-here\n";
echo "# Optional: Custom path (if not using default)\n";
echo "# FIREBASE_SERVICE_ACCOUNT_PATH=your/custom/path/service-account.json\n\n";

echo "Step 4: Test the Setup\n";
echo "---------------------\n";
echo "After placing the file and updating .env, run:\n";
echo "php test_firebase_migration.php\n\n";

echo "Step 5: Install Dependencies\n";
echo "---------------------------\n";
echo "Make sure you have the Google Auth library installed:\n";
echo "composer require google/auth:^1.35\n\n";

echo "Security Notes:\n";
echo "---------------\n";
echo "✓ The file is already added to .gitignore\n";
echo "✓ Keep the file secure and don't commit it to version control\n";
echo "✓ Use appropriate file permissions (readable by web server only)\n\n";

echo "File Permissions (Linux/Unix):\n";
echo "chmod 600 firebase-credentials.json\n\n";

echo "Troubleshooting:\n";
echo "---------------\n";
echo "1. If file not found: Check the file path in .env\n";
echo "2. If JSON errors: Validate the JSON file format\n";
echo "3. If permission errors: Check file permissions\n";
echo "4. If authentication fails: Verify project ID matches\n\n";

echo "Need Help?\n";
echo "----------\n";
echo "Check the FIREBASE_MIGRATION_GUIDE.md file for detailed instructions.\n"; 