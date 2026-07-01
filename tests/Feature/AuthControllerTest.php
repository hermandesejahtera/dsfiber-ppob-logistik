<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use DSFiber\Http\Controllers\API\v1\AuthController;
use DSFiber\Domains\Auth\Services\AuthService;

class AuthControllerTest extends TestCase
{
    private AuthController $authController;
    private AuthService $authService;

    protected function setUp(): void
    {
        // Mock AuthService
        $this->authService = $this->createMock(AuthService::class);
        $this->authController = new AuthController($this->authService);
    }

    public function test_register_with_valid_data()
    {
        $this->authService->method('register')
            ->willReturn(['success' => true, 'message' => 'User registered']);

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/api/v1/auth/register';

        $result = $this->authController->register();

        $this->assertTrue($result['success']);
    }

    public function test_register_with_missing_fields()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/api/v1/auth/register';

        $result = $this->authController->register();

        $this->assertArrayHasKey('error', $result);
        $this->assertEquals(400, $result['code']);
    }

    public function test_login_with_valid_credentials()
    {
        $this->authService->method('login')
            ->willReturn(['success' => true, 'token' => 'jwt-token']);

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/api/v1/auth/login';

        $result = $this->authController->login();

        $this->assertTrue($result['success']);
    }
}
