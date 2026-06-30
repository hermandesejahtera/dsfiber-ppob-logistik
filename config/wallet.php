<?php
/**
 * Wallet & Finance Configuration
 */

return [
    'wallet' => [
        'min_balance' => 10000,
        'max_balance' => 999999999,
    ],

    'ledger' => [
        'double_entry' => true,
        'precision' => 2,
    ],

    'commission' => [
        'network_level' => 3,
        'retail_percentage' => 5,
        'reseller_percentage' => 10,
        'master_percentage' => 15,
    ],

    'report' => [
        'retention_days' => 365,
    ],
];
