<?php

namespace DSFiber\Http\Middleware;

use DSFiber\Core\Security\JwtManager;

/**
 * JWT Authentication Middleware
 */
class AuthGuard
{
    private JwtManager $jwt;

    public function __construct(JwtManager $jwt)
    {
        $this->jwt = $jwt;
    }

    public function handle(): array
    {
        $token = $this->extractToken();

        if (!$token) {
            return ['error' => 'Unauthorized', 'code' => 401];
        }

        $payload = $this->jwt->verify($token);

        if (!$payload) {
            return ['error' => 'Invalid token', 'code' => 401];
        }

        return ['user' => $payload];
    }

    private function extractToken(): ?string
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (preg_match('/Bearer\s+([a-zA-Z0-9\-._~+\/]+=*)/', $header, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
