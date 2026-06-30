<?php

namespace DSFiber\Http\Controllers\Admin;

/**
 * Dashboard Controller
 */
class DashboardController
{
    public function index(): array
    {
        return [
            'success' => true,
            'data' => [
                'total_users' => 0,
                'total_transactions' => 0,
                'total_revenue' => 0,
                'active_sessions' => 0
            ]
        ];
    }
}
