<?php

/**
 * DSFiber PPOB & Logistik System
 * Main Entry Point
 */

require_once __DIR__ . '/vendor/autoload.php';

use DSFiber\Core\Routing\Router;
use DSFiber\Http\Routes\ApiRoutes;
use DSFiber\Http\Routes\AdminRoutes;

// Set error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Load environment variables
require_once __DIR__ . '/config/bootstrap.php';

// Initialize Router
$router = new Router();

// Register Routes
ApiRoutes::register($router);
AdminRoutes::register($router);

// Dispatch
$router->dispatch();
