<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Queue Performance Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for optimizing queue performance
    | specifically for MyUKM real-time features.
    |
    */

    'broadcast_chat' => [
        'queue' => 'high',
        'timeout' => 60,
        'tries' => 3,
        'retry_delay' => 5, // seconds
    ],

    'broadcast_online_status' => [
        'queue' => 'default',
        'timeout' => 30,
        'tries' => 2,
        'retry_delay' => 3, // seconds
    ],

    'monitoring' => [
        'enabled' => env('QUEUE_MONITORING_ENABLED', true),
        'log_slow_jobs' => env('QUEUE_LOG_SLOW_JOBS', true),
        'slow_job_threshold' => env('QUEUE_SLOW_JOB_THRESHOLD', 1000), // milliseconds
    ],

    'optimization' => [
        'batch_size' => env('QUEUE_BATCH_SIZE', 10),
        'sleep_when_empty' => env('QUEUE_SLEEP_WHEN_EMPTY', 3), // seconds
        'max_execution_time' => env('QUEUE_MAX_EXECUTION_TIME', 3600), // seconds
    ],

    'real_time_settings' => [
        'chat_message_priority' => 'high',
        'online_status_priority' => 'default',
        'typing_indicator_priority' => 'low',
        'notification_priority' => 'default',
    ],

];
