CREATE TABLE IF NOT EXISTS commissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    referrer_id BIGINT UNSIGNED NOT NULL,
    referee_id BIGINT UNSIGNED NOT NULL,
    transaction_id BIGINT UNSIGNED,
    level INT NOT NULL,
    percentage DECIMAL(5, 2) NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    status ENUM('PENDING', 'APPROVED', 'PAID', 'CANCELLED') DEFAULT 'PENDING',
    paid_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (referrer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (referee_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id),
    INDEX (referrer_id),
    INDEX (referee_id),
    INDEX (status),
    INDEX (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;