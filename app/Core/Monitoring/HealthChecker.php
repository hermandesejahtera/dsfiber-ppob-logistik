<?php

namespace App\Core\Monitoring;

use App\Infrastructure\Database\Database;
use App\Infrastructure\Cache\Cache;

/**
 * System Health Checker
 * Monitors critical system components
 */
class HealthChecker
{
    private Database $db;
    private Cache $cache;
    private array $checks = [];

    public function __construct(Database $db, Cache $cache)
    {
        $this->db = $db;
        $this->cache = $cache;
    }

    /**
     * Run all health checks
     */
    public function runAllChecks(): array
    {
        return [
            'status' => $this->getOverallStatus(),
            'timestamp' => date('Y-m-d H:i:s'),
            'checks' => [
                'database' => $this->checkDatabase(),
                'cache' => $this->checkCache(),
                'disk' => $this->checkDiskSpace(),
                'memory' => $this->checkMemory(),
                'files' => $this->checkCriticalFiles(),
            ]
        ];
    }

    /**
     * Check database connectivity
     */
    private function checkDatabase(): array
    {
        $startTime = microtime(true);
        
        try {
            $this->db->select('SELECT 1');
            $responseTime = (microtime(true) - $startTime) * 1000;
            
            return [
                'status' => 'UP',
                'response_time_ms' => round($responseTime, 2),
                'message' => 'Database connection successful'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'DOWN',
                'message' => 'Database connection failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check cache connectivity
     */
    private function checkCache(): array
    {
        try {
            $testKey = 'health_check_' . time();
            $this->cache->set($testKey, 'ok', 60);
            $value = $this->cache->get($testKey);
            $this->cache->delete($testKey);
            
            if ($value === 'ok') {
                return [
                    'status' => 'UP',
                    'message' => 'Cache working properly'
                ];
            }
            
            return [
                'status' => 'DOWN',
                'message' => 'Cache read/write failed'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'WARNING',
                'message' => 'Cache unavailable: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check disk space
     */
    private function checkDiskSpace(): array
    {
        $diskFree = disk_free_space(__DIR__);
        $diskTotal = disk_total_space(__DIR__);
        
        if ($diskFree === false) {
            return [
                'status' => 'UNKNOWN',
                'message' => 'Could not determine disk space'
            ];
        }

        $usagePercent = (($diskTotal - $diskFree) / $diskTotal) * 100;
        $status = $usagePercent > 90 ? 'CRITICAL' : ($usagePercent > 75 ? 'WARNING' : 'UP');
        
        return [
            'status' => $status,
            'usage_percent' => round($usagePercent, 2),
            'free_gb' => round($diskFree / (1024 ** 3), 2),
            'total_gb' => round($diskTotal / (1024 ** 3), 2)
        ];
    }

    /**
     * Check memory usage
     */
    private function checkMemory(): array
    {
        $memUsage = memory_get_usage(true);
        $memLimit = ini_get('memory_limit');
        $memPeakUsage = memory_get_peak_usage(true);
        
        return [
            'status' => 'UP',
            'current_usage_mb' => round($memUsage / (1024 ** 2), 2),
            'peak_usage_mb' => round($memPeakUsage / (1024 ** 2), 2),
            'limit' => $memLimit
        ];
    }

    /**
     * Check critical files and directories
     */
    private function checkCriticalFiles(): array
    {
        $criticalPaths = [
            'storage/logs' => storage_path('logs'),
            'storage/cache' => storage_path('cache'),
            'storage/uploads' => storage_path('uploads'),
            'config' => config_path()
        ];

        $results = [];
        foreach ($criticalPaths as $name => $path) {
            $exists = file_exists($path);
            $writable = is_writable($path);
            $status = $exists && $writable ? 'UP' : 'DOWN';
            
            $results[$name] = [
                'status' => $status,
                'exists' => $exists,
                'writable' => $writable
            ];
        }

        return $results;
    }

    /**
     * Determine overall health status
     */
    private function getOverallStatus(): string
    {
        $checks = $this->runAllChecks()['checks'];
        
        foreach ($checks as $check) {
            if (is_array($check) && isset($check['status']) && $check['status'] === 'DOWN') {
                return 'UNHEALTHY';
            }
        }

        return 'HEALTHY';
    }
}
