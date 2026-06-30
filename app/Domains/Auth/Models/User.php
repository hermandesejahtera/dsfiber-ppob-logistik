<?php

namespace DSFiber\Domains\Auth\Models;

/**
 * User Model
 */
class User
{
    public int $id;
    public string $phone;
    public string $pin_hash;
    public string $name;
    public string $email;
    public string $status; // active, inactive, suspended
    public int $kyc_status; // 0: not verified, 1: pending, 2: verified
    public string $created_at;
    public string $updated_at;

    public function __construct(
        string $phone,
        string $pin_hash,
        string $name,
        string $email
    ) {
        $this->phone = $phone;
        $this->pin_hash = $pin_hash;
        $this->name = $name;
        $this->email = $email;
        $this->status = 'active';
        $this->kyc_status = 0;
    }
}
