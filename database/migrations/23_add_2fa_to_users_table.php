<?php

return [
    'name' => 'add_2fa_to_users_table',
    'up' => function($connection) {
        $connection->exec("
            ALTER TABLE users ADD COLUMN (
                two_factor_enabled BOOLEAN DEFAULT FALSE,
                two_factor_method VARCHAR(20),
                two_factor_destination VARCHAR(255),
                backup_codes LONGTEXT,
                last_2fa_at TIMESTAMP NULL
            )
        ");
    },
    'down' => function($connection) {
        $connection->exec("
            ALTER TABLE users DROP COLUMN (
                two_factor_enabled,
                two_factor_method,
                two_factor_destination,
                backup_codes,
                last_2fa_at
            )
        ");
    }
];
