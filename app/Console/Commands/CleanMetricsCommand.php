<?php

namespace App\Console\Commands;

use App\Infrastructure\Database\Database;

/**
 * Clean old metrics command
 * Usage: php app/Console/Commands/CleanMetricsCommand.php
 */
class CleanMetricsCommand
{
    public static function handle()
    {
        $db = Database::getInstance();
        $daysOld = $GLOBALS['argv'][2] ?? 30;
        
        echo "Cleaning metrics older than $daysOld days...\n";
        
        $cutoffTime = date('Y-m-d H:i:s', time() - ($daysOld * 24 * 60 * 60));
        
        $result = $db->delete('metrics', 'recorded_at < ?', [$cutoffTime]);
        
        echo "✓ Removed $result old metric records\n";
        echo "✓ Cleanup completed!\n";
    }
}

// Auto-run if called directly
if (php_sapi_name() === 'cli' && basename($GLOBALS['argv'][0] ?? '') === 'CleanMetricsCommand.php') {
    CleanMetricsCommand::handle();
}
