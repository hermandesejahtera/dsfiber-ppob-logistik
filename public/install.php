<?php
/**
 * DSFiber - Database Installation Script
 * Run migrations & seeders
 */

define('BASE_PATH', dirname(__DIR__));
define('CONFIG_PATH', BASE_PATH . '/config');
define('MIGRATIONS_PATH', BASE_PATH . '/database/migrations');
define('SEEDERS_PATH', BASE_PATH . '/database/seeders');

echo "\n=== DSFiber Database Installation ===\n\n";

try {
    // Load environment
    if (!file_exists(BASE_PATH . '/.env')) {
        throw new Exception('.env file not found. Copy .env.example to .env');
    }

    $env = parse_ini_file(BASE_PATH . '/.env');

    // Connect to database
    $dsn = sprintf(
        'mysql:host=%s;port=%s',
        $env['DB_HOST'] ?? '127.0.0.1',
        $env['DB_PORT'] ?? '3306'
    );

    $pdo = new PDO(
        $dsn,
        $env['DB_USERNAME'] ?? 'root',
        $env['DB_PASSWORD'] ?? ''
    );

    // Create database if not exists
    $dbName = $env['DB_DATABASE'] ?? 'dsfiber_ppob';
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ Database '$dbName' created\n";

    // Select database
    $pdo->exec("USE `$dbName`");

    // Run migrations
    echo "\nRunning migrations...\n";
    $migrations = scandir(MIGRATIONS_PATH);
    foreach ($migrations as $file) {
        if (strpos($file, '.sql') !== false) {
            $sql = file_get_contents(MIGRATIONS_PATH . '/' . $file);
            $pdo->exec($sql);
            echo "✓ Migrated: $file\n";
        }
    }

    // Run seeders
    echo "\nRunning seeders...\n";
    $seeders = scandir(SEEDERS_PATH);
    foreach ($seeders as $file) {
        if (strpos($file, '.sql') !== false) {
            $sql = file_get_contents(SEEDERS_PATH . '/' . $file);
            $pdo->exec($sql);
            echo "✓ Seeded: $file\n";
        }
    }

    echo "\n✅ Installation completed successfully!\n\n";

} catch (Exception $e) {
    echo "\n❌ Installation failed: " . $e->getMessage() . "\n\n";
    exit(1);
}
