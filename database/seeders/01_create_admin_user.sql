INSERT INTO users (
    username, email, phone, password_hash, pin_hash, 
    full_name, user_type, status, kyc_status, two_factor_enabled
) VALUES (
    'admin',
    'admin@dsfiber.local',
    '081234567890',
    SHA2('admin123', 256),
    SHA2('123456', 256),
    'Administrator',
    'ADMIN',
    'ACTIVE',
    'VERIFIED',
    TRUE
);

INSERT INTO wallets (user_id, balance) 
VALUES ((SELECT id FROM users WHERE username = 'admin'), 0);

INSERT INTO networks (user_id) 
VALUES ((SELECT id FROM users WHERE username = 'admin'));