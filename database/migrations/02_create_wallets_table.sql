CREATE TABLE IF NOT EXISTS wallets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    wallet_type ENUM('PPOB', 'LOGISTICS', 'AGENT', 'MASTER', 'SUB_AGENT', 'OWNER') NOT NULL DEFAULT 'PPOB',
    balance DECIMAL(15, 2) NOT NULL DEFAULT 0,
    pending_balance DECIMAL(15, 2) NOT NULL DEFAULT 0,
    hold_balance DECIMAL(15, 2) NOT NULL DEFAULT 0,
    total_income DECIMAL(15, 2) NOT NULL DEFAULT 0,
    total_expense DECIMAL(15, 2) NOT NULL DEFAULT 0,
    external_reference VARCHAR(100) DEFAULT NULL,
    status ENUM('ACTIVE', 'INACTIVE', 'FROZEN') DEFAULT 'ACTIVE',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY uq_user_wallet_type (user_id, wallet_type),
    INDEX idx_user_id (user_id),
    INDEX idx_wallet_type (wallet_type),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;