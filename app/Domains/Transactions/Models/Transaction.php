<?php

namespace DSFiber\Domains\Transactions\Models;

/**
 * Transaction Model
 */
class Transaction
{
    public int $id;
    public int $user_id;
    public int $product_id;
    public int $amount;
    public string $status; // pending, success, failed
    public string $reference_no;
    public ?string $rajabiller_ref;
    public ?string $biteship_ref;
    public array $metadata; // additional data
    public string $created_at;
    public string $updated_at;

    public function __construct(
        int $user_id,
        int $product_id,
        int $amount,
        string $reference_no
    ) {
        $this->user_id = $user_id;
        $this->product_id = $product_id;
        $this->amount = $amount;
        $this->reference_no = $reference_no;
        $this->status = 'pending';
        $this->metadata = [];
    }
}
