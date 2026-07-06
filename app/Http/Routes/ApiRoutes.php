<?php

namespace DSFiber\Http\Routes;

use DSFiber\Core\Routing\Router;
use DSFiber\Http\Controllers\API\v1\AuthController;
use DSFiber\Http\Controllers\API\v1\CatalogController;
use DSFiber\Http\Controllers\API\v1\TransactionController;
use DSFiber\Http\Controllers\API\v1\FinanceController;
use DSFiber\Http\Controllers\API\v1\LogisticsController;
use DSFiber\Http\Controllers\API\v1\HardwareController;
use DSFiber\Domains\Auth\Services\AuthService;
use DSFiber\Domains\Catalog\Services\CatalogService;
use DSFiber\Domains\Catalog\Services\ProductSyncService;
use DSFiber\Domains\Transactions\Services\TransactionService;
use DSFiber\Domains\Finance\Services\FinanceService;
use DSFiber\Domains\Finance\Services\WalletService;
use DSFiber\Domains\Finance\Services\PaymentRequestService;
use DSFiber\Domains\Logistics\Services\WaybillService;
use DSFiber\Domains\Hardware\Services\DeviceService;
use DSFiber\Domains\Auth\Repositories\UserRepository;
use DSFiber\Domains\Catalog\Repositories\ProductRepository;
use DSFiber\Domains\Transactions\Repositories\TransactionRepository;
use DSFiber\Domains\Finance\Repositories\WalletRepository;
use DSFiber\Domains\Finance\Repositories\PaymentRequestRepository;
use DSFiber\Domains\Logistics\Repositories\WaybillRepository;
use DSFiber\Domains\Hardware\Repositories\DeviceTokenRepository;
use DSFiber\Infrastructure\Database\Connection;
use DSFiber\Infrastructure\Cache\Redis;
use DSFiber\Infrastructure\Rajabiller\Client as RajabillerClient;
use DSFiber\Infrastructure\Biteship\Client as BiteshipClient;
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
        $walletRepo = new WalletRepository($db);
        $paymentRequestRepo = new PaymentRequestRepository($db);
        $waybillRepo = new WaybillRepository($db);
        $deviceTokenRepo = new DeviceTokenRepository($db);

        // External clients
        $rajabillerClient = new RajabillerClient();
        $biteshipClient = new BiteshipClient();

        // Services
        $authService = new AuthService($userRepo, $pinHasher, $jwt);
        $catalogService = new CatalogService($productRepo);
        $productSyncService = new ProductSyncService($productRepo, $rajabillerClient);
        $transactionService = new TransactionService($transactionRepo, $productRepo);
        $walletService = new WalletService($walletRepo, $paymentRequestRepo);
        $waybillService = new WaybillService($waybillRepo, $biteshipClient);
        $deviceService = new DeviceService($deviceTokenRepo);

        // Controllers
        $authController = new AuthController($authService);
        $catalogController = new CatalogController($catalogService, $productSyncService);
        $transactionController = new TransactionController($transactionService);
        $financeController = new FinanceController($walletService);
        $logisticsController = new LogisticsController($waybillService);
        $hardwareController = new HardwareController($deviceService);

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
        $router->post('/api/v1/finance/topup-request', fn() => $financeController->requestTopUp());

        // Logistics Routes
        $router->post('/api/v1/logistics/waybill/create', fn() => $logisticsController->createWaybill());
        $router->post('/api/v1/logistics/waybill/details', fn() => $logisticsController->getWaybill());
        $router->post('/api/v1/logistics/waybill/sync', fn() => $logisticsController->syncWaybill());

        // Hardware Routes
        $router->post('/api/v1/hardware/register', fn() => $hardwareController->registerDevice());
        $router->post('/api/v1/hardware/validate', fn() => $hardwareController->validateDevice());

        // Catalog Sync Routes
        $router->post('/api/v1/catalog/products/sync', fn() => $catalogController->syncProducts());
    }
}
