<?php

namespace DSFiber\Infrastructure\Logger;

use DateTime;

/**
 * File Logger
 */
class FileLogger
{
    private string $logPath;
    private string $logLevel;

    public function __construct(string $logPath = 'storage/logs')
    {
        $this->logPath = $logPath;
        if (!is_dir($this->logPath)) {
            mkdir($this->logPath, 0755, true);
        }
    }

    /**
     * Log message
     */
    public function log(string $level, string $message, array $context = []): void
    {
        $timestamp = (new DateTime())->format('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        $logMessage = "[$timestamp] $level: $message$contextStr" . PHP_EOL;

        $logFile = $this->logPath . '/' . strtolower($level) . '.log';
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }

    public function info(string $message, array $context = []): void
    {
        $this->log('INFO', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->log('ERROR', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->log('WARNING', $message, $context);
    }

    public function debug(string $message, array $context = []): void
    {
        $this->log('DEBUG', $message, $context);
    }
}
