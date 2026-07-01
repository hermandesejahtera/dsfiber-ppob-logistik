<?php

/**
 * Database Migration - Create Transactions Table
 */
return [
    'up' => function ($connection) {
        $sql = "
            CREATE TABLE IF NOT EXISTS transactions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                product_id INT NOT NULL,
                amount INT NOT NULL COMMENT 'Transaction amount in IDR',
                status ENUM('pending', 'success', 'failed') DEFAULT 'pending',
                reference_no VARCHAR(50) UNIQUE NOT NULL,
                rajabiller_ref VARCHAR(50) NULL,
                biteship_ref VARCHAR(50) NULL,
                metadata JSON NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                INDEX idx_user_id (user_id),
                INDEX idx_status (status),
                INDEX idx_reference_no (reference_no)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        return $connection->query($sql);
    },

    'down' => function ($connection) {
        return $connection->query('DROP TABLE IF EXISTS transactions');
    }
];
