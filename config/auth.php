<?php
/**
 * Authentication Configuration
 */

return [
    'jwt' => [
        'secret' => $_ENV['JWT_SECRET'] ?? 'change-me',
        'algorithm' => $_ENV['JWT_ALGORITHM'] ?? 'HS256',
        'expire' => $_ENV['JWT_EXPIRE'] ?? 86400, // 24 hours
    ],

    'security' => [
        'pin_salt' => $_ENV['PIN_SALT'] ?? 'change-me',
        'pin_length' => 6,
        'pin_max_attempts' => 3,
        'pin_lockout_minutes' => 15,
    ],

    'encryption' => [
        'key' => $_ENV['ENCRYPTION_KEY'] ?? 'change-me',
        'cipher' => $_ENV['ENCRYPTION_CIPHER'] ?? 'AES-256-CBC',
    ],

    '2fa' => [
        'enabled' => true,
        'driver' => 'otp', // otp, sms, email
        'otp_length' => 6,
        'otp_expire' => 300, // 5 minutes
    ],
];
