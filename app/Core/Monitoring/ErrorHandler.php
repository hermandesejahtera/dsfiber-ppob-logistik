<?php

namespace App\Core\Monitoring;

use App\Core\Logging\Logger;
use Throwable;

/**
 * Centralized Error Handler
 * Handles all application errors and exceptions
 */
class ErrorHandler
{
    private Logger $logger;
    private bool $debug;

    public function __construct(bool $debug = false)
    {
        $this->debug = $debug;
        $this->logger = new Logger('error');
        
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
    }

    /**
     * Handle PHP errors
     */
    public function handleError(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        $errorTypes = [
            E_ERROR => 'ERROR',
            E_WARNING => 'WARNING',
            E_PARSE => 'PARSE',
            E_NOTICE => 'NOTICE',
            E_CORE_ERROR => 'CORE_ERROR',
            E_CORE_WARNING => 'CORE_WARNING',
            E_COMPILE_ERROR => 'COMPILE_ERROR',
            E_COMPILE_WARNING => 'COMPILE_WARNING',
            E_USER_ERROR => 'USER_ERROR',
            E_USER_WARNING => 'USER_WARNING',
            E_USER_NOTICE => 'USER_NOTICE',
            E_STRICT => 'STRICT',
            E_RECOVERABLE_ERROR => 'RECOVERABLE_ERROR',
            E_DEPRECATED => 'DEPRECATED',
            E_USER_DEPRECATED => 'USER_DEPRECATED',
        ];

        $type = $errorTypes[$errno] ?? 'UNKNOWN';
        
        $this->logger->error("[$type] $errstr", [
            'file' => $errfile,
            'line' => $errline,
            'errno' => $errno
        ]);

        // Don't execute PHP internal error handler
        return true;
    }

    /**
     * Handle exceptions
     */
    public function handleException(Throwable $exception): void
    {
        $this->logger->critical($exception->getMessage(), [
            'exception' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]);

        $this->sendErrorResponse($exception);
    }

    /**
     * Send error response to client
     */
    private function sendErrorResponse(Throwable $exception): void
    {
        http_response_code(500);
        header('Content-Type: application/json');
        
        $response = [
            'success' => false,
            'message' => 'An error occurred',
            'error' => $exception->getMessage()
        ];

        if ($this->debug) {
            $response['debug'] = [
                'exception' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString()
            ];
        }

        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
