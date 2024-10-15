<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Seeder Tracking Table
    |--------------------------------------------------------------------------
    |
    | The database table used to store seeder execution history.
    |
    */
    'table' => env('SEEDER_TRACKER_TABLE', 'seeder_tracking'),

    /*
    |--------------------------------------------------------------------------
    | Auto-Track Seeders
    |--------------------------------------------------------------------------
    |
    | Automatically track seeders that extend TrackableSeeder.
    |
    */
    'auto_track' => env('SEEDER_AUTO_TRACK', true),

    /*
    |--------------------------------------------------------------------------
    | Prevent Duplicate Execution
    |--------------------------------------------------------------------------
    |
    | Prevent seeders from running multiple times.
    |
    */
    'prevent_duplicates' => env('SEEDER_PREVENT_DUPLICATES', true),

    /*
    |--------------------------------------------------------------------------
    | Strict Environments
    |--------------------------------------------------------------------------
    |
    | Environments where duplicate prevention is always enforced.
    |
    */
    'strict_environments' => ['production'],

    /*
    |--------------------------------------------------------------------------
    | Performance Monitoring
    |--------------------------------------------------------------------------
    |
    | Enable detailed performance monitoring and memory tracking.
    |
    */
    'performance_monitoring' => env('SEEDER_PERFORMANCE_MONITORING', true),

    /*
    |--------------------------------------------------------------------------
    | Batch Size for Status Display
    |--------------------------------------------------------------------------
    |
    | Number of seeders to display per page in status commands.
    |
    */
    'status_page_size' => env('SEEDER_STATUS_PAGE_SIZE', 50),

    /*
    |--------------------------------------------------------------------------
    | Automatic Cleanup
    |--------------------------------------------------------------------------
    |
    | Automatically clean up old tracking records after specified days.
    | Set to null to disable automatic cleanup.
    |
    */
    'cleanup_after_days' => env('SEEDER_CLEANUP_AFTER_DAYS', null),

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    |
    | Configure notifications for seeder execution events.
    |
    */
    'notifications' => [
        'enabled' => env('SEEDER_NOTIFICATIONS_ENABLED', false),
        'channels' => ['mail', 'slack'],
        'on_failure' => true,
        'on_slow_execution' => true,
        'slow_threshold_ms' => 5000,
    ],
];
