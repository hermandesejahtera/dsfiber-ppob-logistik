<?php

namespace App\Http\Controllers\Api;

use App\Core\Monitoring\HealthChecker;
use App\Infrastructure\Database\Database;
use App\Infrastructure\Cache\Cache;

/**
 * Health Check Controller
 * Provides system health status endpoint
 */
class HealthController
{
    private HealthChecker $healthChecker;

    public function __construct(Database $db, Cache $cache)
    {
        $this->healthChecker = new HealthChecker($db, $cache);
    }

    /**
     * GET /api/health
     * Returns system health status
     */
    public function status()
    {
        $health = $this->healthChecker->runAllChecks();
        
        http_response_code($health['status'] === 'HEALTHY' ? 200 : 503);
        
        return [
            'success' => $health['status'] === 'HEALTHY',
            'data' => $health
        ];
    }

    /**
     * GET /api/health/detailed
     * Returns detailed health information
     */
    public function detailed()
    {
        $health = $this->healthChecker->runAllChecks();
        
        return [
            'success' => true,
            'data' => $health
        ];
    }
}
