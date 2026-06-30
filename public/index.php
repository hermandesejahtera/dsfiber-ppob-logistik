<?php
/**
 * DSFiber - Mobile API Gateway
 * Entry point untuk semua request API
 */

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');
define('STORAGE_PATH', BASE_PATH . '/storage');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-PIN');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(json_encode(['status' => 'ok']));
}

try {
    // Load environment
    if (file_exists(BASE_PATH . '/.env')) {
        $dotenv = parse_ini_file(BASE_PATH . '/.env');
        foreach ($dotenv as $key => $value) {
            $_ENV[$key] = $value;
        }
    }

    // Autoload
    require BASE_PATH . '/vendor/autoload.php';

    // Load config
    $config = require CONFIG_PATH . '/app.php';

    // Route dispatcher
    $method = $_SERVER['REQUEST_METHOD'];
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $path = str_replace('/api/', '', $path);

    // Debug info
    if ($_ENV['APP_DEBUG'] ?? false) {
        error_log("[$method] $path");
    }

    // Response
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'DSFiber API Gateway Ready',
        'version' => '1.0.0',
        'timestamp' => date('c')
    ]);

} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'debug' => $_ENV['APP_DEBUG'] ?? false ? $e->getTraceAsString() : null
    ]);
}
