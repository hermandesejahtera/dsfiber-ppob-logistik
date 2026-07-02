<?php

return [
    'name' => 'create_oauth_connections_table',
    'up' => function($connection) {
        $connection->exec("
            CREATE TABLE IF NOT EXISTS oauth_connections (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NOT NULL,
                provider VARCHAR(50) NOT NULL,
                provider_id VARCHAR(255) NOT NULL,
                access_token LONGTEXT,
                refresh_token LONGTEXT,
                expires_at TIMESTAMP NULL,
                connected_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_provider (user_id, provider),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function($connection) {
        $connection->exec("DROP TABLE IF EXISTS oauth_connections");
    }
];
