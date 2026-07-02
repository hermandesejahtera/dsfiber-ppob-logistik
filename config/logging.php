<?php

/**
 * Logging Configuration
 * 
 * Defines logging channels and their settings
 */

return [
    'default' => env('LOG_CHANNEL', 'stack'),

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single', 'error'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/app.log'),
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'error' => [
            'driver' => 'single',
            'path' => storage_path('logs/error.log'),
            'level' => 'error',
        ],

        'audit' => [
            'driver' => 'single',
            'path' => storage_path('logs/audit.log'),
            'level' => 'info',
        ],

        'admin' => [
            'driver' => 'single',
            'path' => storage_path('logs/admin.log'),
            'level' => 'info',
        ],

        'database' => [
            'driver' => 'single',
            'path' => storage_path('logs/database.log'),
            'level' => 'debug',
        ],
    ],
];
