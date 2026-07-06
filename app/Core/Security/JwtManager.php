<?php

namespace DSFiber\Core\Security;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * JWT Token Management
 */
class JwtManager
{
    private string $secret;
    private string $algorithm;
    private int $expire;

    public function __construct(string $secret, string $algorithm = 'HS256', int $expire = 86400)
    {
        $this->secret = $secret;
        $this->algorithm = $algorithm;
        $this->expire = $expire;
    }

    /**
     * Generate token
     */
    public function generate(array $payload): string
    {
        $payload['iat'] = time();
        $payload['exp'] = time() + $this->expire;
        return JWT::encode($payload, $this->secret, $this->algorithm);
    }

    /**
     * Verify & decode token
     */
    public function verify(string $token): ?object
    {
        try {
            return JWT::decode($token, new Key($this->secret, $this->algorithm));
        } catch (\Exception $e) {
            return null;
        }
    }
}
