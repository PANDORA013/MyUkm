<?php

return [
    'default' => env('BROADCAST_DRIVER', 'log'),
    'connections' => [
        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true,
                'encrypted' => true,
                'host' => env('PUSHER_HOST') ?: 'api-'.env('PUSHER_APP_CLUSTER', 'mt1').'.pusher.com',
                'port' => env('PUSHER_PORT', 443),
                'scheme' => env('PUSHER_SCHEME', 'https'),
                // OPTIMIZED: Ultra-fast broadcasting settings for real-time responsiveness
                'timeout' => 5,              // Reduced timeout for faster response
                'connect_timeout' => 3,      // Faster connection timeout
            ],
            'client_options' => [
                // OPTIMIZED: Guzzle client options for maximum speed
                'timeout' => 5,              // Request timeout
                'connect_timeout' => 3,      // Connection timeout
                'http_errors' => false,      // Don't throw on HTTP errors
                'verify' => true,            // SSL verification
            ],
        ],
        
        // OPTIMIZED: Sync fallback for instant local testing
        'sync' => [
            'driver' => 'sync',
        ],
        
        // OPTIMIZED: Log fallback for debugging
        'log' => [
            'driver' => 'log',
        ],
    ],
];