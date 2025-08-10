<?php

/**
 * Firebase Setup Script for GoDaddy/Shared Hosting
 * 
 * This script helps you set up Firebase credentials for GoDaddy hosting
 * where you don't have access to the storage directory.
 * 
 * Usage: php setup_firebase_godaddy.php
 */

echo "Firebase Setup for GoDaddy/Shared Hosting\n";
echo "=========================================\n\n";

echo "This script will help you set up Firebase credentials for your GoDaddy hosting.\n\n";

echo "Step 1: Download Service Account Key\n";
echo "-----------------------------------\n";
echo "1. Go to https://console.firebase.google.com/\n";
echo "2. Select your project\n";
echo "3. Go to Project Settings (gear icon)\n";
echo "4. Go to Service Accounts tab\n";
echo "5. Click 'Generate new private key'\n";
echo "6. Download the JSON file\n\n";

echo "Step 2: Prepare Environment Variable\n";
echo "-----------------------------------\n";
echo "1. Open the downloaded JSON file\n";
echo "2. Copy the ENTIRE content (including all brackets and quotes)\n";
echo "3. Make sure it's all on one line\n\n";

echo "Step 3: Update Your .env File\n";
echo "-----------------------------\n";
echo "Add these lines to your .env file:\n\n";

echo "FIREBASE_PROJECT_ID=your-project-id-here\n";
echo "FIREBASE_SERVICE_ACCOUNT_JSON={\"type\":\"service_account\",\"project_id\":\"your-project-id\",...}\n\n";

echo "Important Notes:\n";
echo "- Replace 'your-project-id-here' with your actual Firebase project ID\n";
echo "- Replace the JSON content with your actual service account JSON\n";
echo "- Make sure the JSON is all on one line\n";
echo "- Don't add any extra quotes around the JSON\n\n";

echo "Step 4: Test the Setup\n";
echo "---------------------\n";
echo "After updating your .env file, run:\n";
echo "php test_firebase_migration.php\n\n";

echo "Step 5: Install Dependencies\n";
echo "---------------------------\n";
echo "Make sure you have the Google Auth library installed:\n";
echo "composer require google/auth:^1.35\n\n";

echo "Troubleshooting:\n";
echo "---------------\n";
echo "1. If you get JSON parsing errors, make sure the JSON is valid\n";
echo "2. If you get authentication errors, check your project ID\n";
echo "3. Make sure the service account has Firebase Messaging permissions\n";
echo "4. Check your .env file syntax (no extra quotes or spaces)\n\n";

echo "Security Reminder:\n";
echo "-----------------\n";
echo "- Never commit your .env file to version control\n";
echo "- Keep your service account credentials secure\n";
echo "- Rotate your service account keys regularly\n\n";

echo "Need Help?\n";
echo "----------\n";
echo "Check the FIREBASE_MIGRATION_GUIDE.md file for detailed instructions.\n"; 