<?php

/**
 * Database Migration - Create Balances Table
 */
return [
    'up' => function ($connection) {
        $sql = "
            CREATE TABLE IF NOT EXISTS balances (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL UNIQUE,
                balance INT DEFAULT 0 COMMENT 'Balance in IDR',
                last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_user_id (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        return $connection->query($sql);
    },

    'down' => function ($connection) {
        return $connection->query('DROP TABLE IF EXISTS balances');
    }
];
