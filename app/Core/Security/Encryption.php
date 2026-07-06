<?php

namespace DSFiber\Core\Security;

/**
 * AES-256-CBC Encryption & Decryption
 */
class Encryption
{
    private string $key;
    private string $cipher;

    public function __construct(string $key, string $cipher = 'AES-256-CBC')
    {
        $this->key = $key;
        $this->cipher = $cipher;
    }

    /**
     * Encrypt data
     */
    public function encrypt(string $data): string
    {
        $iv = openssl_random_pseudo_bytes(16);
        $encrypted = openssl_encrypt($data, $this->cipher, $this->key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    /**
     * Decrypt data
     */
    public function decrypt(string $encrypted): string
    {
        $data = base64_decode($encrypted);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        return openssl_decrypt($encrypted, $this->cipher, $this->key, 0, $iv);
    }
}
