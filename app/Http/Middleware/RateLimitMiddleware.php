<?php

namespace App\Http\Middleware;

use App\Core\Security\RateLimiter;
use App\Infrastructure\Cache\Cache;

/**
 * Rate Limiting Middleware
 * Limits requests based on IP or user
 */
class RateLimitMiddleware
{
    private RateLimiter $rateLimiter;

    public function __construct()
    {
        $this->rateLimiter = new RateLimiter(Cache::getInstance());
    }

    /**
     * Handle the request
     */
    public function handle($request, $next, array $options = [])
    {
        $identifier = $this->getIdentifier($options);
        $maxAttempts = $options['max_attempts'] ?? 60;
        $windowSeconds = $options['window_seconds'] ?? 60;

        if (!$this->rateLimiter->allow($identifier, $maxAttempts, $windowSeconds)) {
            http_response_code(429);
            header('Content-Type: application/json');
            header('Retry-After: ' . $windowSeconds);
            
            echo json_encode([
                'success' => false,
                'message' => 'Too many requests. Please try again later.',
                'retry_after' => $windowSeconds
            ]);
            exit;
        }

        // Add rate limit headers to response
        header('X-RateLimit-Limit: ' . $maxAttempts);
        header('X-RateLimit-Remaining: ' . $this->rateLimiter->getRemainingAttempts($identifier, $maxAttempts));
        header('X-RateLimit-Reset: ' . (time() + $windowSeconds));

        return $next($request);
    }

    /**
     * Get identifier (user ID or IP address)
     */
    private function getIdentifier(array $options): string
    {
        if (!empty($_SESSION['user_id'])) {
            return "user:{$_SESSION['user_id']}";
        }

        return "ip:{$_SERVER['REMOTE_ADDR']}";
    }
}
