<?php

namespace DSFiber\Domains\Transactions\Repositories;

use DSFiber\Domains\Transactions\Models\Transaction;
use DSFiber\Infrastructure\Database\Connection;

/**
 * Transaction Repository
 */
class TransactionRepository
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function create(Transaction $transaction): int
    {
        $metadata = json_encode($transaction->metadata);
        $stmt = $this->db->prepare(
            'INSERT INTO transactions (user_id, product_id, amount, status, reference_no, metadata) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param(
            'iiisss',
            $transaction->user_id,
            $transaction->product_id,
            $transaction->amount,
            $transaction->status,
            $transaction->reference_no,
            $metadata
        );
        $stmt->execute();
        return $this->db->getConnection()->insert_id;
    }

    public function findById(int $id): ?Transaction
    {
        $stmt = $this->db->prepare('SELECT * FROM transactions WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (!$row) return null;

        $transaction = new Transaction(
            $row['user_id'],
            $row['product_id'],
            $row['amount'],
            $row['reference_no']
        );
        $transaction->id = $row['id'];
        $transaction->status = $row['status'];
        $transaction->metadata = json_decode($row['metadata'], true);
        return $transaction;
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare('UPDATE transactions SET status = ? WHERE id = ?');
        $stmt->bind_param('si', $status, $id);
        return $stmt->execute();
    }
}
