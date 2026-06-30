<?php
/**
 * Rajabiller PPOB API Configuration
 */

return [
    'api_key' => $_ENV['RAJABILLER_API_KEY'] ?? '',
    'username' => $_ENV['RAJABILLER_USERNAME'] ?? '',
    'url' => $_ENV['RAJABILLER_URL'] ?? 'https://api.rajabiller.com',
    'webhook_secret' => $_ENV['RAJABILLER_WEBHOOK_SECRET'] ?? '',
    'timeout' => 30,
    'retry_attempts' => 3,
    'retry_delay' => 1000, // milliseconds
];
