<?php

/**
 * Database Migration - Create Products Table
 */
return [
    'up' => function ($connection) {
        $sql = "
            CREATE TABLE IF NOT EXISTS products (
                id INT AUTO_INCREMENT PRIMARY KEY,
                code VARCHAR(50) UNIQUE NOT NULL,
                name VARCHAR(100) NOT NULL,
                category VARCHAR(50) NOT NULL,
                price INT NOT NULL COMMENT 'Cost price in IDR',
                selling_price INT NOT NULL COMMENT 'Selling price in IDR',
                provider VARCHAR(50) NOT NULL,
                status ENUM('active', 'inactive') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_category (category),
                INDEX idx_status (status),
                INDEX idx_provider (provider)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        return $connection->query($sql);
    },

    'down' => function ($connection) {
        return $connection->query('DROP TABLE IF EXISTS products');
    }
];
