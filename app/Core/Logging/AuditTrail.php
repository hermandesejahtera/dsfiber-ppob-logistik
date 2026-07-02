<?php

namespace App\Core\Logging;

use App\Infrastructure\Database\Database;
use DateTime;

/**
 * Audit Trail Service
 * Tracks all significant actions in the system
 */
class AuditTrail
{
    private Database $db;
    private Logger $logger;

    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->logger = new Logger('audit');
    }

    /**
     * Log a user action
     */
    public function logAction(
        ?int $userId,
        string $action,
        string $subject,
        string $description,
        array $changes = [],
        string $ipAddress = null
    ): void {
        $ipAddress = $ipAddress ?? ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        
        $logData = [
            'user_id' => $userId,
            'action' => $action,
            'subject' => $subject,
            'description' => $description,
            'changes' => !empty($changes) ? json_encode($changes) : null,
            'ip_address' => $ipAddress,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'timestamp' => (new DateTime())->format('Y-m-d H:i:s')
        ];

        try {
            $this->db->insert('audit_logs', $logData);
        } catch (\Exception $e) {
            $this->logger->error('Failed to log audit trail', ['error' => $e->getMessage()]);
        }

        $this->logger->info("Action: $action - $subject", $logData);
    }

    /**
     * Log transaction
     */
    public function logTransaction(
        int $userId,
        string $type,
        string $status,
        float $amount,
        string $reference,
        array $metadata = []
    ): void {
        $this->logAction(
            $userId,
            'TRANSACTION',
            $type,
            "Transaction $status: $reference",
            [
                'type' => $type,
                'status' => $status,
                'amount' => $amount,
                'reference' => $reference,
                'metadata' => $metadata
            ]
        );
    }

    /**
     * Log login attempt
     */
    public function logLoginAttempt(string $username, bool $success, string $ipAddress = null): void
    {
        $ipAddress = $ipAddress ?? ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        
        $action = $success ? 'LOGIN_SUCCESS' : 'LOGIN_FAILED';
        $this->logAction(
            null,
            $action,
            'AUTH',
            "Login attempt: $username",
            ['username' => $username, 'success' => $success],
            $ipAddress
        );
    }

    /**
     * Log security event
     */
    public function logSecurityEvent(string $eventType, string $description, array $data = []): void
    {
        $this->logAction(
            null,
            'SECURITY_EVENT',
            'SECURITY',
            $description,
            array_merge(['event_type' => $eventType], $data)
        );
    }

    /**
     * Get audit logs
     */
    public function getLogs(int $limit = 100, int $offset = 0): array
    {
        try {
            return $this->db->select(
                'SELECT * FROM audit_logs ORDER BY timestamp DESC LIMIT ? OFFSET ?',
                [$limit, $offset]
            );
        } catch (\Exception $e) {
            $this->logger->error('Failed to fetch audit logs', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get user's audit trail
     */
    public function getUserAuditTrail(int $userId, int $limit = 50): array
    {
        try {
            return $this->db->select(
                'SELECT * FROM audit_logs WHERE user_id = ? ORDER BY timestamp DESC LIMIT ?',
                [$userId, $limit]
            );
        } catch (\Exception $e) {
            $this->logger->error('Failed to fetch user audit trail', ['error' => $e->getMessage()]);
            return [];
        }
    }
}
