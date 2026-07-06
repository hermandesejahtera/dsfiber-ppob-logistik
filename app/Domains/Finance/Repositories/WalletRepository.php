<?php

namespace DSFiber\Domains\Finance\Repositories;

use DSFiber\Domains\Finance\Models\Wallet;
use DSFiber\Infrastructure\Database\Connection;

/**
 * Wallet Repository
 */
class WalletRepository
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function findByUserAndType(int $userId, string $walletType): ?Wallet
    {
        $stmt = $this->db->prepare('SELECT * FROM wallets WHERE user_id = ? AND wallet_type = ? LIMIT 1');
        $stmt->bind_param('is', $userId, $walletType);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (!$row) {
            return null;
        }

        return $this->mapRowToWallet($row);
    }

    public function create(Wallet $wallet): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO wallets (user_id, wallet_type, balance, pending_balance, hold_balance, total_income, total_expense, external_reference, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $externalReference = $wallet->external_reference ?? '';
        $stmt->bind_param(
            'isddddsss',
            $wallet->user_id,
            $wallet->wallet_type,
            $wallet->balance,
            $wallet->pending_balance,
            $wallet->hold_balance,
            $wallet->total_income,
            $wallet->total_expense,
            $externalReference,
            $wallet->status
        );
        $stmt->execute();
        return $this->db->getConnection()->insert_id;
    }

    public function update(Wallet $wallet): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE wallets SET balance = ?, pending_balance = ?, hold_balance = ?, total_income = ?, total_expense = ?, external_reference = ?, status = ?, updated_at = NOW() WHERE id = ?'
        );
        $externalReference = $wallet->external_reference ?? '';
        $stmt->bind_param(
            'ddddsssi',
            $wallet->balance,
            $wallet->pending_balance,
            $wallet->hold_balance,
            $wallet->total_income,
            $wallet->total_expense,
            $externalReference,
            $wallet->status,
            $wallet->id
        );

        return $stmt->execute();
    }

    public function createOrGet(int $userId, string $walletType): Wallet
    {
        $wallet = $this->findByUserAndType($userId, $walletType);

        if ($wallet) {
            return $wallet;
        }

        $wallet = new Wallet($userId, $walletType);
        $walletId = $this->create($wallet);
        $wallet->id = $walletId;
        return $wallet;
    }

    private function mapRowToWallet(array $row): Wallet
    {
        $wallet = new Wallet((int) $row['user_id'], $row['wallet_type']);
        $wallet->id = (int) $row['id'];
        $wallet->balance = (float) $row['balance'];
        $wallet->pending_balance = (float) $row['pending_balance'];
        $wallet->hold_balance = (float) $row['hold_balance'];
        $wallet->total_income = (float) $row['total_income'];
        $wallet->total_expense = (float) $row['total_expense'];
        $wallet->external_reference = $row['external_reference'] ?? null;
        $wallet->status = $row['status'];
        $wallet->created_at = $row['created_at'] ?? null;
        $wallet->updated_at = $row['updated_at'] ?? null;

        return $wallet;
    }
}
