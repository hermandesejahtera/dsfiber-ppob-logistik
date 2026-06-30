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
            'INSERT INTO users (phone, pin_hash, name, email) VALUES (?, ?, ?, ?)'
        );
        $stmt->bind_param('ssss', $user->phone, $user->pin_hash, $user->name, $user->email);
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

        $user = new User($row['phone'], $row['pin_hash'], $row['name'], $row['email']);
        $user->id = $row['id'];
        $user->status = $row['status'];
        $user->kyc_status = $row['kyc_status'];
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

        $user = new User($row['phone'], $row['pin_hash'], $row['name'], $row['email']);
        $user->id = $row['id'];
        $user->status = $row['status'];
        $user->kyc_status = $row['kyc_status'];
        return $user;
    }
}
