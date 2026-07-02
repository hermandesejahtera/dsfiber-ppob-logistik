<?php

namespace App\Core\Security;

use App\Infrastructure\Cache\Cache;
use App\Core\Logging\Logger;

/**
 * Rate Limiter
 * Prevents abuse by limiting requests per user/IP
 */
class RateLimiter
{
    private Cache $cache;
    private Logger $logger;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
        $this->logger = new Logger('security');
    }

    /**
     * Check if request is allowed
     */
    public function allow(string $identifier, int $maxAttempts = 60, int $windowSeconds = 60): bool
    {
        $key = "rate_limit:{$identifier}";
        $attempts = $this->cache->get($key) ?? 0;

        if ($attempts >= $maxAttempts) {
            $this->logger->warning("Rate limit exceeded for: {$identifier}", [
                'attempts' => $attempts,
                'max_attempts' => $maxAttempts
            ]);
            return false;
        }

        $this->cache->increment($key, 1, $windowSeconds);
        return true;
    }

    /**
     * Get remaining attempts
     */
    public function getRemainingAttempts(string $identifier, int $maxAttempts = 60): int
    {
        $key = "rate_limit:{$identifier}";
        $attempts = $this->cache->get($key) ?? 0;
        return max(0, $maxAttempts - $attempts);
    }

    /**
     * Reset rate limit for identifier
     */
    public function reset(string $identifier): void
    {
        $key = "rate_limit:{$identifier}";
        $this->cache->delete($key);
    }

    /**
     * Get current attempts
     */
    public function getAttempts(string $identifier): int
    {
        $key = "rate_limit:{$identifier}";
        return $this->cache->get($key) ?? 0;
    }
}
