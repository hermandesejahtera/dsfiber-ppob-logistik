<?php

namespace DSFiber\Domains\Finance\Services;

use DSFiber\Infrastructure\Cache\Redis;

/**
 * Finance Service - Balance & Wallet Management
 */
class FinanceService
{
    private Redis $cache;

    public function __construct(Redis $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Get user balance
     */
    public function getBalance(int $userId): int
    {
        $cacheKey = "balance:user:{$userId}";
        $balance = $this->cache->get($cacheKey);

        if ($balance === null) {
            // In production, fetch from database
            $balance = 0;
            $this->cache->set($cacheKey, $balance, 3600);
        }

        return $balance;
    }

    /**
     * Deduct balance
     */
    public function deductBalance(int $userId, int $amount): bool
    {
        $currentBalance = $this->getBalance($userId);

        if ($currentBalance < $amount) {
            return false;
        }

        $newBalance = $currentBalance - $amount;
        $cacheKey = "balance:user:{$userId}";
        $this->cache->set($cacheKey, $newBalance, 3600);

        return true;
    }

    /**
     * Add balance
     */
    public function addBalance(int $userId, int $amount): bool
    {
        $currentBalance = $this->getBalance($userId);
        $newBalance = $currentBalance + $amount;
        $cacheKey = "balance:user:{$userId}";
        $this->cache->set($cacheKey, $newBalance, 3600);

        return true;
    }
}
