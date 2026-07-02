<?php

namespace App\Console\Commands;

use App\Core\Logging\Logger;

/**
 * Clear old logs command
 * Usage: php app/Console/Commands/ClearLogsCommand.php
 */
class ClearLogsCommand
{
    public static function handle()
    {
        $daysOld = $GLOBALS['argv'][2] ?? 7;
        
        echo "Clearing logs older than $daysOld days...\n";
        
        $channels = ['app', 'error', 'audit', 'admin'];
        $totalRemoved = 0;
        
        foreach ($channels as $channel) {
            $logger = new Logger($channel);
            $removed = $logger->clearOldLogs($daysOld);
            $totalRemoved += $removed;
            echo "  ✓ Cleared $removed logs from $channel channel\n";
        }
        
        echo "\nTotal logs removed: $totalRemoved\n";
        echo "✓ Cleanup completed!\n";
    }
}

// Auto-run if called directly
if (php_sapi_name() === 'cli' && basename($GLOBALS['argv'][0] ?? '') === 'ClearLogsCommand.php') {
    ClearLogsCommand::handle();
}
