<?php

namespace App\Http\Middleware;

use App\Core\Security\TwoFactorAuthManager;
use App\Infrastructure\Cache\Cache;

/**
 * Two-Factor Authentication Middleware
 * Enforces 2FA for protected endpoints
 */
class TwoFactorMiddleware
{
    private TwoFactorAuthManager $tfaManager;

    public function __construct()
    {
        $this->tfaManager = new TwoFactorAuthManager(Cache::getInstance());
    }

    /**
     * Handle the request
     */
    public function handle($request, $next, array $options = [])
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            http_response_code(401);
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        // Check if 2FA is required
        $require2fa = $options['require'] ?? false;
        $skipEndpoints = $options['skip'] ?? [];

        if ($require2fa && !in_array($_SERVER['REQUEST_URI'], $skipEndpoints)) {
            // Check if 2FA is verified in current session
            if (!($_SESSION['2fa_verified'] ?? false)) {
                http_response_code(403);
                return [
                    'success' => false,
                    'message' => 'Two-factor authentication required',
                    'action' => 'require_2fa'
                ];
            }
        }

        return $next($request);
    }
}
