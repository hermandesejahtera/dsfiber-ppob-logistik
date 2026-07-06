<?php

return [
    // Database Configuration
    'database' => [
        'host' => env('DB_HOST', 'localhost'),
        'user' => env('DB_USER', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'database' => env('DB_NAME', 'dsfiber'),
        'port' => env('DB_PORT', 3306),
    ],

    // Redis Configuration
    'redis' => [
        'host' => env('REDIS_HOST', 'localhost'),
        'port' => env('REDIS_PORT', 6379),
        'password' => env('REDIS_PASSWORD', null),
    ],

    // JWT Configuration
    'jwt' => [
        'secret' => env('JWT_SECRET', 'dsfiber-secret-key'),
        'algorithm' => 'HS256',
        'expiry' => 86400, // 24 hours
    ],

    // API Configuration
    'api' => [
        'version' => 'v1',
        'base_url' => env('API_URL', 'http://localhost'),
        'timeout' => 30,
    ],

    // External APIs
    'external_apis' => [
        'rajabiller' => [
            'base_url' => env('RAJABILLER_URL', 'https://api.rajabiller.com'),
            'api_key' => env('RAJABILLER_KEY', ''),
            'api_secret' => env('RAJABILLER_SECRET', ''),
        ],
        'biteship' => [
            'base_url' => env('BITESHIP_URL', 'https://api.biteship.com'),
            'api_key' => env('BITESHIP_KEY', ''),
        ],
    ],

    // Logging
    'logging' => [
        'level' => env('LOG_LEVEL', 'info'),
        'path' => env('LOG_PATH', 'storage/logs'),
    ],
];
