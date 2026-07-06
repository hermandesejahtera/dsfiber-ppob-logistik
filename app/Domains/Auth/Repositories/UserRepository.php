<?php

namespace DSFiber\Domains\Auth\Repositories;

use DSFiber\Domains\Auth\Models\User;
use DSFiber\Infrastructure\Database\Connection;

/**
 * User Repository
 */
class UserRepository
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function create(User $user): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (username, phone, password_hash, pin_hash, full_name, email, user_type, status, kyc_status, two_factor_enabled) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        $status = $user->status;
        $kycStatus = $user->kyc_status;
        $twoFactorEnabled = $user->two_factor_enabled ? 1 : 0;

        $stmt->bind_param(
            'sssssisssi',
            $user->username,
            $user->phone,
            $user->password_hash,
            $user->pin_hash,
            $user->full_name,
            $user->email,
            $user->user_type,
            $status,
            $kycStatus,
            $twoFactorEnabled
        );

        $stmt->execute();
        return $this->db->getConnection()->insert_id;
    }

    public function findByPhone(string $phone): ?User
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE phone = ? LIMIT 1');
        $stmt->bind_param('s', $phone);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (!$row) return null;

        $user = new User(
            $row['username'],
            $row['phone'],
            $row['password_hash'],
            $row['pin_hash'],
            $row['full_name'],
            $row['email'],
            $row['user_type'] ?? 'RETAIL'
        );

        $user->id = (int) $row['id'];
        $user->status = $row['status'];
        $user->kyc_status = $row['kyc_status'];
        $user->identity_number = $row['identity_number'] ?? '';
        $user->identity_type = $row['identity_type'] ?? 'KTP';
        $user->identity_verified = (bool) ($row['identity_verified'] ?? false);
        $user->kyc_rejection_reason = $row['kyc_rejection_reason'] ?? null;
        $user->two_factor_method = $row['two_factor_method'] ?? 'OTP';
        $user->last_login_at = $row['last_login_at'] ?? null;
        $user->last_login_ip = $row['last_login_ip'] ?? null;
        $user->created_at = $row['created_at'] ?? null;
        $user->updated_at = $row['updated_at'] ?? null;
        $user->deleted_at = $row['deleted_at'] ?? null;

        return $user;
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (!$row) return null;

        $user = new User(
            $row['username'],
            $row['phone'],
            $row['password_hash'],
            $row['pin_hash'],
            $row['full_name'],
            $row['email'],
            $row['user_type'] ?? 'RETAIL'
        );

        $user->id = (int) $row['id'];
        $user->status = $row['status'];
        $user->kyc_status = $row['kyc_status'];
        $user->identity_number = $row['identity_number'] ?? '';
        $user->identity_type = $row['identity_type'] ?? 'KTP';
        $user->identity_verified = (bool) ($row['identity_verified'] ?? false);
        $user->kyc_rejection_reason = $row['kyc_rejection_reason'] ?? null;
        $user->two_factor_method = $row['two_factor_method'] ?? 'OTP';
        $user->last_login_at = $row['last_login_at'] ?? null;
        $user->last_login_ip = $row['last_login_ip'] ?? null;
        $user->created_at = $row['created_at'] ?? null;
        $user->updated_at = $row['updated_at'] ?? null;
        $user->deleted_at = $row['deleted_at'] ?? null;

        return $user;
    }
}
