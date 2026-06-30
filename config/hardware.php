<?php
/**
 * Hardware & Device Binding Configuration
 */

return [
    'binding' => [
        'enabled' => true,
        'max_devices' => 5,
        'validation_type' => 'serial_number',
    ],

    'printer' => [
        'driver' => 'thermal',
        'port' => '/dev/ttyUSB0',
        'baud_rate' => 9600,
    ],
];
