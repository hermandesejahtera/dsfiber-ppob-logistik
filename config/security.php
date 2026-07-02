<?php

return [
    'rate_limit' => [
        'default' => [
            'max_attempts' => 60,
            'window_seconds' => 60
        ],
        'auth' => [
            'max_attempts' => 5,
            'window_seconds' => 300  // 5 attempts per 5 minutes
        ],
        'api' => [
            'max_attempts' => 1000,
            'window_seconds' => 3600  // 1000 requests per hour
        ],
        'transaction' => [
            'max_attempts' => 100,
            'window_seconds' => 3600  // 100 transactions per hour
        ]
    ],

    'oauth' => [
        'google' => [
            'enabled' => env('OAUTH_GOOGLE_ENABLED', false),
            'client_id' => env('OAUTH_GOOGLE_CLIENT_ID'),
            'client_secret' => env('OAUTH_GOOGLE_CLIENT_SECRET')
        ],
        'github' => [
            'enabled' => env('OAUTH_GITHUB_ENABLED', false),
            'client_id' => env('OAUTH_GITHUB_CLIENT_ID'),
            'client_secret' => env('OAUTH_GITHUB_CLIENT_SECRET')
        ],
        'facebook' => [
            'enabled' => env('OAUTH_FACEBOOK_ENABLED', false),
            'client_id' => env('OAUTH_FACEBOOK_CLIENT_ID'),
            'client_secret' => env('OAUTH_FACEBOOK_CLIENT_SECRET')
        ]
    ],

    '2fa' => [
        'enabled' => env('2FA_ENABLED', true),
        'methods' => ['sms', 'email', 'whatsapp'],
        'otp_length' => 6,
        'otp_expiry' => 300,  // 5 minutes
        'max_attempts' => 3,
        'backup_codes_count' => 10
    ]
];
