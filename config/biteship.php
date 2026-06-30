<?php
/**
 * Biteship Logistics API Configuration
 */

return [
    'api_key' => $_ENV['BITESHIP_API_KEY'] ?? '',
    'url' => $_ENV['BITESHIP_URL'] ?? 'https://api.biteship.com',
    'webhook_secret' => $_ENV['BITESHIP_WEBHOOK_SECRET'] ?? '',
    'timeout' => 30,
    'retry_attempts' => 3,
    'retry_delay' => 1000, // milliseconds
];
