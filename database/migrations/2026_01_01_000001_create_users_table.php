<?php

/**
 * Database Migration - Create Users Table
 */
return [
    'up' => function ($connection) {
        $sql = "
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                phone VARCHAR(20) UNIQUE NOT NULL,
                pin_hash VARCHAR(255) NOT NULL,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
                kyc_status INT DEFAULT 0 COMMENT '0: not verified, 1: pending, 2: verified',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_phone (phone),
                INDEX idx_email (email),
                INDEX idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        return $connection->query($sql);
    },

    'down' => function ($connection) {
        return $connection->query('DROP TABLE IF EXISTS users');
    }
];
