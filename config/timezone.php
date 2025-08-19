<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Timezone Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains timezone-related configuration for the application.
    | It helps ensure consistent timezone handling across different parts
    | of the application (web, API, database, etc.).
    |
    */

    // Default application timezone
    'app_timezone' => env('APP_TIMEZONE', 'Asia/Kolkata'),

    // Database timezone (should be UTC for consistency)
    'database_timezone' => env('DB_TIMEZONE', 'UTC'),

    // API response timezone (for mobile apps)
    'api_timezone' => env('API_TIMEZONE', 'Asia/Kolkata'),

    // Date format for API responses
    'api_date_format' => env('API_DATE_FORMAT', 'Y-m-d H:i:s'),

    // Date format for web display
    'web_date_format' => env('WEB_DATE_FORMAT', 'M d, Y H:i'),

    // Timezone offset for Asia/Kolkata (IST)
    'ist_offset' => '+05:30',

    // Timezone offset for UTC
    'utc_offset' => '+00:00',
]; 