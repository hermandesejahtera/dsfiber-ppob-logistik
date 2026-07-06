<?php

namespace DSFiber\Domains\Finance\Models;

/**
 * Wallet Model
 */
class Wallet
{
    public int $id;
    public int $user_id;
    public string $wallet_type;
    public float $balance;
    public float $pending_balance;
    public float $hold_balance;
    public float $total_income;
    public float $total_expense;
    public ?string $external_reference;
    public string $status;
    public ?string $created_at;
    public ?string $updated_at;

    public function __construct(int $user_id, string $wallet_type = 'PPOB')
    {
        $this->user_id = $user_id;
        $this->wallet_type = $wallet_type;
        $this->balance = 0.0;
        $this->pending_balance = 0.0;
        $this->hold_balance = 0.0;
        $this->total_income = 0.0;
        $this->total_expense = 0.0;
        $this->external_reference = null;
        $this->status = 'ACTIVE';
    }
}
