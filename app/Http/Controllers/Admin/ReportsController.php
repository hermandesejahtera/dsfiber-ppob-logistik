<?php

namespace DSFiber\Http\Controllers\Admin;

/**
 * Reports Controller
 */
class ReportsController
{
    public function getDailyReport(): array
    {
        return [
            'success' => true,
            'data' => [
                'date' => date('Y-m-d'),
                'transactions' => 0,
                'revenue' => 0
            ]
        ];
    }

    public function getMonthlyReport(): array
    {
        return [
            'success' => true,
            'data' => [
                'month' => date('Y-m'),
                'transactions' => 0,
                'revenue' => 0
            ]
        ];
    }
}
