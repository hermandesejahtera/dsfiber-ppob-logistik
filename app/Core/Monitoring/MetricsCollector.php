<?php

namespace App\Core\Monitoring;

use App\Infrastructure\Database\Database;

/**
 * Metrics Collector
 * Collects application performance metrics
 */
class MetricsCollector
{
    private Database $db;
    private float $startTime;
    private array $metrics = [];

    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->startTime = microtime(true);
    }

    /**
     * Record a metric
     */
    public function record(string $name, float $value, array $tags = [], string $unit = ''): void
    {
        $this->metrics[] = [
            'name' => $name,
            'value' => $value,
            'tags' => $tags,
            'unit' => $unit,
            'timestamp' => microtime(true)
        ];
    }

    /**
     * Record request time
     */
    public function recordRequestTime(string $endpoint, float $responseTime, int $statusCode): void
    {
        $this->record('request_time_ms', $responseTime * 1000, [
            'endpoint' => $endpoint,
            'status_code' => $statusCode
        ], 'ms');
    }

    /**
     * Record database query time
     */
    public function recordQueryTime(string $query, float $executionTime): void
    {
        $this->record('db_query_time_ms', $executionTime * 1000, [
            'query_hash' => substr(hash('md5', $query), 0, 8)
        ], 'ms');
    }

    /**
     * Record cache hit/miss
     */
    public function recordCacheOperation(string $operation, bool $hit, float $responseTime): void
    {
        $this->record('cache_operation', $hit ? 1 : 0, [
            'operation' => $operation,
            'hit' => $hit
        ]);
    }

    /**
     * Record API call
     */
    public function recordApiCall(string $service, int $statusCode, float $responseTime): void
    {
        $this->record('external_api_call', $responseTime * 1000, [
            'service' => $service,
            'status_code' => $statusCode
        ], 'ms');
    }

    /**
     * Get current request duration
     */
    public function getRequestDuration(): float
    {
        return microtime(true) - $this->startTime;
    }

    /**
     * Get all collected metrics
     */
    public function getMetrics(): array
    {
        return $this->metrics;
    }

    /**
     * Persist metrics to storage
     */
    public function persist(): bool
    {
        if (empty($this->metrics)) {
            return true;
        }

        try {
            foreach ($this->metrics as $metric) {
                $this->db->insert('metrics', [
                    'name' => $metric['name'],
                    'value' => $metric['value'],
                    'tags' => json_encode($metric['tags']),
                    'unit' => $metric['unit'],
                    'recorded_at' => date('Y-m-d H:i:s', (int)$metric['timestamp'])
                ]);
            }
            return true;
        } catch (\Exception $e) {
            error_log('Failed to persist metrics: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get metrics summary
     */
    public function getSummary(string $metricName, int $minutes = 60): array
    {
        try {
            $cutoffTime = date('Y-m-d H:i:s', time() - ($minutes * 60));
            
            $results = $this->db->select(
                'SELECT AVG(value) as avg, MIN(value) as min, MAX(value) as max, COUNT(*) as count '
                . 'FROM metrics WHERE name = ? AND recorded_at >= ?',
                [$metricName, $cutoffTime]
            );

            return $results[0] ?? [
                'avg' => 0,
                'min' => 0,
                'max' => 0,
                'count' => 0
            ];
        } catch (\Exception $e) {
            error_log('Failed to get metrics summary: ' . $e->getMessage());
            return [];
        }
    }
}
