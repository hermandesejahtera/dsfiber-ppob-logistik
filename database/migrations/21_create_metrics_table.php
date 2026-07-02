<?php

return [
    'name' => 'create_metrics_table',
    'up' => function($connection) {
        $connection->exec("
            CREATE TABLE IF NOT EXISTS metrics (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                value DECIMAL(15, 4) NOT NULL,
                tags JSON,
                unit VARCHAR(20),
                recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_name (name),
                INDEX idx_recorded_at (recorded_at),
                INDEX idx_name_timestamp (name, recorded_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function($connection) {
        $connection->exec("DROP TABLE IF EXISTS metrics");
    }
];
