<?php
/**
 * Catalog & Product Configuration
 */

return [
    'sync' => [
        'auto_sync' => true,
        'sync_interval' => 3600, // 1 hour
        'timeout' => 30,
    ],

    'pricing' => [
        'markup_type' => 'percentage', // percentage, fixed
        'default_markup' => 10, // 10%
        'cache_duration' => 1800, // 30 minutes
    ],

    'categories' => [
        'PULSA',
        'DATA',
        'LISTRIK',
        'AIR',
        'ASURANSI',
        'VOUCHER',
    ],
];
