<?php

namespace DSFiber\Domains\Finance\Models;

/**
 * Payment Request Model
 */
class PaymentRequest
{
    public int $id;
    public int $user_id;
    public int $wallet_id;
    public float $amount;
    public string $bank_reference;
    public string $status;
    public ?string $notes;
    public ?string $created_at;
    public ?string $updated_at;

    public function __construct(int $user_id, int $wallet_id, float $amount, string $bank_reference)
    {
        $this->user_id = $user_id;
        $this->wallet_id = $wallet_id;
        $this->amount = $amount;
        $this->bank_reference = $bank_reference;
        $this->status = 'PENDING';
        $this->notes = null;
    }
}
