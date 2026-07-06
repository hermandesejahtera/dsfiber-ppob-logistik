<?php

namespace DSFiber\Http\Routes;

use DSFiber\Core\Routing\Router;
use DSFiber\Http\Controllers\Admin\DashboardController;
use DSFiber\Http\Controllers\Admin\ReportsController;

/**
 * Admin Routes
 */
class AdminRoutes
{
    public static function register(Router $router): void
    {
        $dashboardController = new DashboardController();
        $reportsController = new ReportsController();

        // Dashboard Routes
        $router->get('/admin/dashboard', fn() => $dashboardController->index());

        // Reports Routes
        $router->get('/admin/reports/daily', fn() => $reportsController->getDailyReport());
        $router->get('/admin/reports/monthly', fn() => $reportsController->getMonthlyReport());
    }
}
