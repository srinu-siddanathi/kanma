# Timezone Configuration Guide

## Overview
This guide explains the changes made to configure the Laravel application to use **Asia/Kolkata (GMT+5:30)** timezone globally instead of UTC.

## Changes Made

### 1. Application Configuration (`config/app.php`)
- **File**: `config/app.php`
- **Change**: Updated the default timezone from 'UTC' to 'Asia/Kolkata'
- **Line**: 67
```php
'timezone' => env('APP_TIMEZONE', 'Asia/Kolkata'),
```

### 2. Service Provider Configuration (`app/Providers/AppServiceProvider.php`)
- **File**: `app/Providers/AppServiceProvider.php`
- **Changes**:
  - Added Carbon import
  - Set PHP default timezone using `date_default_timezone_set()`
  - Set Carbon's default timezone using `Carbon::setDefaultTimezone()`

```php
use Carbon\Carbon;

public function boot(): void
{
    // Set the default timezone for the application
    date_default_timezone_set(config('app.timezone'));
    
    // Set Carbon's default timezone
    Carbon::setDefaultTimezone(config('app.timezone'));
}
```

### 3. Database Configuration (`config/database.php`)
- **File**: `config/database.php`
- **Changes**: Added timezone configuration for MySQL and MariaDB connections
  - Added `'timezone' => '+05:30'` to connection arrays
  - Added `PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '+05:30'"` to options

## What This Affects

### 1. New Records
- All new `created_at` and `updated_at` timestamps will be stored in Asia/Kolkata timezone
- All new timestamp fields created by Laravel will use the correct timezone

### 2. Existing Records
- **Important**: Existing timestamps in the database will remain as they were stored (UTC)
- Only new records will use the new timezone
- If you need to convert existing timestamps, you'll need to create a separate migration

### 3. Application Behavior
- `now()` helper function will return Asia/Kolkata time
- `Carbon::now()` will return Asia/Kolkata time
- All date/time operations will use Asia/Kolkata timezone
- Database queries will use Asia/Kolkata timezone for new records

## Testing the Configuration

### 1. Run the Test Script
```bash
php test_timezone.php
```

This script will check:
- Application timezone configuration
- PHP default timezone
- Carbon timezone
- Current time in different formats
- Database session timezone (if connected)

### 2. Expected Output
```
=== Timezone Configuration Test ===

1. Application Timezone: Asia/Kolkata
2. PHP Default Timezone: Asia/Kolkata
3. Carbon Default Timezone: Asia/Kolkata
4. Current Time (Carbon): 2024-01-15 14:30:00 IST
5. Current Time (PHP): 2024-01-15 14:30:00 IST
6. Database Session Timezone: +05:30
7. Sample Timestamp: 2024-01-15 14:30:00 Asia/Kolkata

=== Test Complete ===
Expected timezone: Asia/Kolkata (GMT+5:30)
If all values show Asia/Kolkata or +05:30, the configuration is working correctly.
```

### 3. Manual Testing
You can also test by:
1. Creating a new record in any table
2. Checking that the `created_at` and `updated_at` timestamps show the correct timezone
3. Using `now()` in your application code

## Environment Variables

If you want to override the timezone via environment variables, you can add to your `.env` file:
```
APP_TIMEZONE=Asia/Kolkata
```

## Important Notes

### 1. Existing Data
- Existing timestamps in your database will remain in UTC
- Only new records will use the Asia/Kolkata timezone
- If you need to convert existing data, consider creating a migration

### 2. Database Server
- Ensure your MySQL/MariaDB server supports the timezone
- The timezone should be available in your database server's timezone tables

### 3. Deployment
- Make sure to clear configuration cache after deployment:
```bash
php artisan config:clear
php artisan cache:clear
```

### 4. API Responses
- All API responses that include timestamps will now show Asia/Kolkata time
- Frontend applications should be updated to handle the new timezone if they were expecting UTC

## Troubleshooting

### 1. Timezone Not Applied
- Clear Laravel cache: `php artisan config:clear`
- Restart your web server
- Check if the AppServiceProvider is being loaded

### 2. Database Connection Issues
- Verify your database server supports the timezone
- Check database logs for timezone-related errors

### 3. Existing Data Issues
- Existing timestamps will remain in UTC
- Consider creating a migration to convert existing data if needed

## Migration for Existing Data (Optional)

If you need to convert existing timestamps from UTC to Asia/Kolkata, you can create a migration:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Example: Convert users table timestamps
        DB::statement("
            UPDATE users 
            SET created_at = CONVERT_TZ(created_at, 'UTC', 'Asia/Kolkata'),
                updated_at = CONVERT_TZ(updated_at, 'UTC', 'Asia/Kolkata')
        ");
    }

    public function down()
    {
        // Convert back to UTC if needed
        DB::statement("
            UPDATE users 
            SET created_at = CONVERT_TZ(created_at, 'Asia/Kolkata', 'UTC'),
                updated_at = CONVERT_TZ(updated_at, 'Asia/Kolkata', 'UTC')
        ");
    }
};
```

**Warning**: Only run this migration if you're sure about converting existing data, as it will permanently change your timestamp values. 