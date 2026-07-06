<?php

namespace DSFiber\Domains\Finance\Repositories;

use DSFiber\Domains\Finance\Models\PaymentRequest;
use DSFiber\Infrastructure\Database\Connection;

/**
 * Payment Request Repository
 */
class PaymentRequestRepository
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function create(PaymentRequest $request): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO payment_requests (user_id, wallet_id, amount, bank_reference, status, notes) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param(
            'iiddss',
            $request->user_id,
            $request->wallet_id,
            $request->amount,
            $request->bank_reference,
            $request->status,
            $request->notes
        );
        $stmt->execute();
        return $this->db->getConnection()->insert_id;
    }

    public function findById(int $id): ?PaymentRequest
    {
        $stmt = $this->db->prepare('SELECT * FROM payment_requests WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (!$row) {
            return null;
        }

        $request = new PaymentRequest((int) $row['user_id'], (int) $row['wallet_id'], (float) $row['amount'], $row['bank_reference']);
        $request->id = (int) $row['id'];
        $request->status = $row['status'];
        $request->notes = $row['notes'] ?? null;
        $request->created_at = $row['created_at'] ?? null;
        $request->updated_at = $row['updated_at'] ?? null;
        return $request;
    }

    public function updateStatus(int $id, string $status, ?string $notes = null): bool
    {
        $stmt = $this->db->prepare('UPDATE payment_requests SET status = ?, notes = ?, updated_at = NOW() WHERE id = ?');
        $stmt->bind_param('ssi', $status, $notes, $id);
        return $stmt->execute();
    }
}
