<?php

namespace DSFiber\Domains\Hardware\Repositories;

use DSFiber\Domains\Hardware\Models\DeviceToken;
use DSFiber\Infrastructure\Database\Connection;

/**
 * Device token repository
 */
class DeviceTokenRepository
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function create(DeviceToken $deviceToken): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO device_tokens (user_id, device_type, device_token, expires_at, status) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->bind_param(
            'issss',
            $deviceToken->user_id,
            $deviceToken->device_type,
            $deviceToken->device_token,
            $deviceToken->expires_at,
            $deviceToken->status
        );
        $stmt->execute();
        return $this->db->getConnection()->insert_id;
    }

    public function findActiveByToken(string $token): ?DeviceToken
    {
        $stmt = $this->db->prepare('SELECT * FROM device_tokens WHERE device_token = ? AND status = "ACTIVE" AND expires_at > NOW() LIMIT 1');
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (!$row) {
            return null;
        }

        return $this->mapRowToDeviceToken($row);
    }

    public function revoke(int $id): bool
    {
        $stmt = $this->db->prepare('UPDATE device_tokens SET status = "REVOKED", updated_at = NOW() WHERE id = ?');
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    private function mapRowToDeviceToken(array $row): DeviceToken
    {
        $deviceToken = new DeviceToken(
            (int) $row['user_id'],
            $row['device_type'],
            $row['device_token'],
            $row['expires_at']
        );

        $deviceToken->id = (int) $row['id'];
        $deviceToken->status = $row['status'];
        $deviceToken->created_at = $row['created_at'] ?? null;
        $deviceToken->updated_at = $row['updated_at'] ?? null;

        return $deviceToken;
    }
}
