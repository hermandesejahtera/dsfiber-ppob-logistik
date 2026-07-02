<?php

namespace App\Core\Logging;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Centralized Logging System
 * Handles all application logging with different channels
 */
class Logger implements LoggerInterface
{
    private string $logPath;
    private string $channel;
    private array $context = [];

    public function __construct(string $channel = 'app')
    {
        $this->channel = $channel;
        $this->logPath = storage_path('logs');
        $this->ensureLogDirectory();
    }

    /**
     * Log an arbitrary message
     */
    public function log($level, $message, array $context = []): void
    {
        $this->write($level, $message, $context);
    }

    /**
     * System is unusable
     */
    public function emergency($message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * Action must be taken immediately
     */
    public function alert($message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * Critical conditions
     */
    public function critical($message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action
     */
    public function error($message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors
     */
    public function warning($message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * Normal but significant events
     */
    public function notice($message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * Interesting events
     */
    public function info($message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * Detailed debug information
     */
    public function debug($message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * Write log to file
     */
    private function write(string $level, string $message, array $context = []): void
    {
        $timestamp = date('Y-m-d H:i:s.u');
        $logFile = $this->logPath . '/' . strtolower($this->channel) . '.log';
        
        $logMessage = sprintf(
            "[%s] [%s] [%s] %s%s\n",
            $timestamp,
            strtoupper($level),
            $this->channel,
            $message,
            !empty($context) ? ' ' . json_encode($context) : ''
        );

        error_log($logMessage, 3, $logFile);
    }

    /**
     * Ensure log directory exists
     */
    private function ensureLogDirectory(): void
    {
        if (!is_dir($this->logPath)) {
            mkdir($this->logPath, 0755, true);
        }
    }

    /**
     * Get logs for specific channel
     */
    public function getLogsFromFile(int $lines = 100): array
    {
        $logFile = $this->logPath . '/' . strtolower($this->channel) . '.log';
        
        if (!file_exists($logFile)) {
            return [];
        }

        $file = new \SplFileObject($logFile, 'r');
        $file->seek(PHP_INT_MAX);
        $totalLines = $file->key();
        
        $startLine = max(0, $totalLines - $lines);
        $file->seek($startLine);
        
        $logs = [];
        while (!$file->eof()) {
            $line = $file->fgets();
            if (!empty($line)) {
                $logs[] = $line;
            }
        }
        
        return $logs;
    }

    /**
     * Clear old logs
     */
    public function clearOldLogs(int $daysOld = 7): int
    {
        $logFile = $this->logPath . '/' . strtolower($this->channel) . '.log';
        
        if (!file_exists($logFile)) {
            return 0;
        }

        $file = fopen($logFile, 'r');
        $keepLogs = [];
        $cutoffTime = time() - ($daysOld * 24 * 60 * 60);
        $removedCount = 0;

        while ($line = fgets($file)) {
            if (preg_match('/\[(\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2})/', $line, $matches)) {
                $logTime = strtotime($matches[1]);
                if ($logTime > $cutoffTime) {
                    $keepLogs[] = $line;
                } else {
                    $removedCount++;
                }
            }
        }
        fclose($file);

        file_put_contents($logFile, implode('', $keepLogs));
        return $removedCount;
    }
}
