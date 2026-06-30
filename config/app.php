<?php
/**
 * Application Configuration
 */

return [
    'name' => $_ENV['APP_NAME'] ?? 'DSFiber PPOB',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => $_ENV['APP_DEBUG'] ?? false,
    'url' => $_ENV['APP_URL'] ?? 'http://localhost:8000',
    'key' => $_ENV['APP_KEY'] ?? '',

    'timezone' => 'Asia/Jakarta',
    'locale' => 'id_ID',

    'providers' => [
        'auth',
        'database',
        'cache',
        'event',
    ],
];
