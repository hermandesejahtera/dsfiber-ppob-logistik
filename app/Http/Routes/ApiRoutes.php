<?php

namespace DSFiber\Http\Routes;

use DSFiber\Core\Routing\Router;
use DSFiber\Http\Controllers\API\v1\AuthController;
use DSFiber\Http\Controllers\API\v1\CatalogController;
use DSFiber\Http\Controllers\API\v1\TransactionController;
use DSFiber\Http\Controllers\API\v1\FinanceController;
use DSFiber\Domains\Auth\Services\AuthService;
use DSFiber\Domains\Catalog\Services\CatalogService;
use DSFiber\Domains\Transactions\Services\TransactionService;
use DSFiber\Domains\Finance\Services\FinanceService;
use DSFiber\Domains\Auth\Repositories\UserRepository;
use DSFiber\Domains\Catalog\Repositories\ProductRepository;
use DSFiber\Domains\Transactions\Repositories\TransactionRepository;
use DSFiber\Infrastructure\Database\Connection;
use DSFiber\Infrastructure\Cache\Redis;
use DSFiber\Core\Security\PinHasher;
use DSFiber\Core\Security\JwtManager;

/**
 * API Routes
 */
class ApiRoutes
{
    public static function register(Router $router): void
    {
        // Initialize dependencies
        $db = Connection::getInstance($_ENV);
        $cache = new Redis();
        $pinHasher = new PinHasher();
        $jwt = new JwtManager();

        // Repositories
        $userRepo = new UserRepository($db);
        $productRepo = new ProductRepository($db);
        $transactionRepo = new TransactionRepository($db);

        // Services
        $authService = new AuthService($userRepo, $pinHasher, $jwt);
        $catalogService = new CatalogService($productRepo);
        $transactionService = new TransactionService($transactionRepo, $productRepo);
        $financeService = new FinanceService($cache);

        // Controllers
        $authController = new AuthController($authService);
        $catalogController = new CatalogController($catalogService);
        $transactionController = new TransactionController($transactionService);
        $financeController = new FinanceController($financeService);

        // Auth Routes
        $router->post('/api/v1/auth/register', fn() => $authController->register());
        $router->post('/api/v1/auth/login', fn() => $authController->login());

        // Catalog Routes
        $router->get('/api/v1/catalog/products', fn() => $catalogController->getAllProducts());
        $router->post('/api/v1/catalog/products/category', fn() => $catalogController->getProductsByCategory());
        $router->post('/api/v1/catalog/products/details', fn() => $catalogController->getProductDetails());

        // Transaction Routes
        $router->post('/api/v1/transactions/create', fn() => $transactionController->createTransaction());
        $router->post('/api/v1/transactions/details', fn() => $transactionController->getTransactionDetails());

        // Finance Routes
        $router->post('/api/v1/finance/balance', fn() => $financeController->getBalance());
        $router->post('/api/v1/finance/deduct', fn() => $financeController->deductBalance());
        $router->post('/api/v1/finance/add', fn() => $financeController->addBalance());
    }
}
