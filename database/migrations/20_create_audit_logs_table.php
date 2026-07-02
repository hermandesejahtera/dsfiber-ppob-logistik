<?php

return [
    'name' => 'create_audit_logs_table',
    'up' => function($connection) {
        $connection->exec("
            CREATE TABLE IF NOT EXISTS audit_logs (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NULL,
                action VARCHAR(100) NOT NULL,
                subject VARCHAR(100) NOT NULL,
                description TEXT,
                changes LONGTEXT,
                ip_address VARCHAR(45),
                user_agent VARCHAR(500),
                timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_user_id (user_id),
                INDEX idx_action (action),
                INDEX idx_timestamp (timestamp),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function($connection) {
        $connection->exec("DROP TABLE IF EXISTS audit_logs");
    }
];
