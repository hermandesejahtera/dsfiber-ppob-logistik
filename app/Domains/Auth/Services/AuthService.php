<?php

namespace DSFiber\Domains\Auth\Services;

use DSFiber\Domains\Auth\Models\User;
use DSFiber\Domains\Auth\Repositories\UserRepository;
use DSFiber\Core\Security\PinHasher;
use DSFiber\Core\Security\JwtManager;

/**
 * Authentication Service
 */
class AuthService
{
    private UserRepository $userRepo;
    private PinHasher $pinHasher;
    private JwtManager $jwt;

    public function __construct(
        UserRepository $userRepo,
        PinHasher $pinHasher,
        JwtManager $jwt
    ) {
        $this->userRepo = $userRepo;
        $this->pinHasher = $pinHasher;
        $this->jwt = $jwt;
    }

    /**
     * Register new user
     */
    public function register(string $phone, string $pin, string $name, string $email): array
    {
        // Check if user already exists
        if ($this->userRepo->findByPhone($phone)) {
            return ['success' => false, 'message' => 'User already exists'];
        }

        $pinHash = $this->pinHasher->hash($pin);
        $user = new User($phone, $pinHash, $name, $email);
        $userId = $this->userRepo->create($user);

        return ['success' => true, 'user_id' => $userId];
    }

    /**
     * Login user
     */
    public function login(string $phone, string $pin): array
    {
        $user = $this->userRepo->findByPhone($phone);

        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }

        if (!$this->pinHasher->verify($pin, $user->pin_hash)) {
            return ['success' => false, 'message' => 'Invalid PIN'];
        }

        if ($user->status !== 'active') {
            return ['success' => false, 'message' => 'User account inactive'];
        }

        $token = $this->jwt->generate(['user_id' => $user->id, 'phone' => $user->phone]);

        return ['success' => true, 'token' => $token, 'user_id' => $user->id];
    }
}
