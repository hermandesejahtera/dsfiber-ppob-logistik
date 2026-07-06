<?php

namespace DSFiber\Http\Controllers\API\v1;

use DSFiber\Domains\Auth\Services\AuthService;

/**
 * Authentication Controller
 */
class AuthController
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['phone'], $data['pin'], $data['name'], $data['email'])) {
            return ['error' => 'Missing required fields', 'code' => 400];
        }

        $result = $this->authService->register(
            $data['phone'],
            $data['pin'],
            $data['name'],
            $data['email']
        );

        return $result;
    }

    public function login(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['phone'], $data['pin'])) {
            return ['error' => 'Missing phone or pin', 'code' => 400];
        }

        $result = $this->authService->login($data['phone'], $data['pin']);

        return $result;
    }
}
