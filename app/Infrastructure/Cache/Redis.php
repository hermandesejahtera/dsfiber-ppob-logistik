<?php

namespace DSFiber\Infrastructure\Cache;

use Redis as RedisClient;

/**
 * Redis Cache Driver
 */
class Redis
{
    private RedisClient $redis;
    private int $ttl;

    public function __construct(string $host = 'localhost', int $port = 6379, int $ttl = 3600)
    {
        $this->redis = new RedisClient();
        $this->redis->connect($host, $port);
        $this->ttl = $ttl;
    }

    /**
     * Set cache
     */
    public function set(string $key, mixed $value, int $ttl = null): bool
    {
        $ttl = $ttl ?? $this->ttl;
        return $this->redis->setex($key, $ttl, serialize($value));
    }

    /**
     * Get cache
     */
    public function get(string $key): mixed
    {
        $value = $this->redis->get($key);
        return $value ? unserialize($value) : null;
    }

    /**
     * Delete cache
     */
    public function delete(string $key): bool
    {
        return (bool) $this->redis->del($key);
    }

    /**
     * Clear all cache
     */
    public function flush(): bool
    {
        return $this->redis->flushDb();
    }

    /**
     * Check if key exists
     */
    public function has(string $key): bool
    {
        return $this->redis->exists($key) > 0;
    }
}
