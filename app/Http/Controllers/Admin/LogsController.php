<?php

namespace App\Http\Controllers\Admin;

use App\Core\Logging\Logger;
use App\Core\Logging\AuditTrail;
use App\Infrastructure\Database\Database;

/**
 * Logs Management Controller
 * Admin panel for viewing and managing logs
 */
class LogsController
{
    private Logger $logger;
    private AuditTrail $auditTrail;
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->logger = new Logger('admin');
        $this->auditTrail = new AuditTrail($db);
    }

    /**
     * GET /admin/logs
     * Display logs dashboard
     */
    public function index()
    {
        return [
            'success' => true,
            'data' => [
                'message' => 'Logs dashboard'
            ]
        ];
    }

    /**
     * GET /admin/logs/application
     * Get application logs
     */
    public function applicationLogs()
    {
        $logger = new Logger('app');
        $logs = $logger->getLogsFromFile(500);
        
        return [
            'success' => true,
            'data' => [
                'logs' => $logs,
                'total' => count($logs)
            ]
        ];
    }

    /**
     * GET /admin/logs/audit
     * Get audit trail logs
     */
    public function auditLogs()
    {
        $page = $_GET['page'] ?? 1;
        $perPage = $_GET['per_page'] ?? 50;
        $offset = ($page - 1) * $perPage;
        
        $logs = $this->auditTrail->getLogs($perPage, $offset);
        
        return [
            'success' => true,
            'data' => [
                'logs' => $logs,
                'page' => $page,
                'per_page' => $perPage,
                'total' => count($logs)
            ]
        ];
    }

    /**
     * GET /admin/logs/user/{userId}
     * Get specific user's audit trail
     */
    public function userAuditLog($userId)
    {
        $logs = $this->auditTrail->getUserAuditTrail($userId);
        
        return [
            'success' => true,
            'data' => [
                'user_id' => $userId,
                'logs' => $logs,
                'total' => count($logs)
            ]
        ];
    }

    /**
     * GET /admin/logs/errors
     * Get error logs
     */
    public function errorLogs()
    {
        $logger = new Logger('error');
        $logs = $logger->getLogsFromFile(200);
        
        return [
            'success' => true,
            'data' => [
                'logs' => $logs,
                'total' => count($logs)
            ]
        ];
    }

    /**
     * DELETE /admin/logs/clear
     * Clear old logs
     */
    public function clearOldLogs()
    {
        $daysOld = $_POST['days_old'] ?? 7;
        
        $logger = new Logger('app');
        $removedApp = $logger->clearOldLogs($daysOld);
        
        $errorLogger = new Logger('error');
        $removedError = $errorLogger->clearOldLogs($daysOld);
        
        $this->logger->info('Cleared old logs', [
            'days_old' => $daysOld,
            'removed_app' => $removedApp,
            'removed_error' => $removedError
        ]);
        
        return [
            'success' => true,
            'message' => 'Logs cleared successfully',
            'data' => [
                'removed_app_logs' => $removedApp,
                'removed_error_logs' => $removedError
            ]
        ];
    }
}
