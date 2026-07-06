<?php

namespace DSFiber\Domains\Auth\Models;

/**
 * User Model
 */
class User
{
    public int $id;
    public string $username;
    public string $phone;
    public string $password_hash;
    public string $pin_hash;
    public string $full_name;
    public string $email;
    public string $identity_number;
    public string $identity_type;
    public bool $identity_verified;
    public string $user_type; // RETAIL, RESELLER, MASTER, ADMIN
    public string $status; // ACTIVE, INACTIVE, SUSPENDED, BANNED
    public string $kyc_status; // PENDING, VERIFIED, REJECTED
    public ?string $kyc_rejection_reason;
    public bool $two_factor_enabled;
    public string $two_factor_method;
    public ?string $last_login_at;
    public ?string $last_login_ip;
    public ?string $created_at;
    public ?string $updated_at;
    public ?string $deleted_at;

    public function __construct(
        string $username,
        string $phone,
        string $password_hash,
        string $pin_hash,
        string $full_name,
        string $email,
        string $user_type = 'RETAIL'
    ) {
        $this->username = $username;
        $this->phone = $phone;
        $this->password_hash = $password_hash;
        $this->pin_hash = $pin_hash;
        $this->full_name = $full_name;
        $this->email = $email;
        $this->identity_number = '';
        $this->identity_type = 'KTP';
        $this->identity_verified = false;
        $this->user_type = $user_type;
        $this->status = 'ACTIVE';
        $this->kyc_status = 'PENDING';
        $this->kyc_rejection_reason = null;
        $this->two_factor_enabled = false;
        $this->two_factor_method = 'OTP';
    }
}
