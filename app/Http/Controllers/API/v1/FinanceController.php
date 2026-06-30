<?php

namespace DSFiber\Http\Controllers\API\v1;

use DSFiber\Domains\Finance\Services\FinanceService;

/**
 * Finance Controller
 */
class FinanceController
{
    private FinanceService $financeService;

    public function __construct(FinanceService $financeService)
    {
        $this->financeService = $financeService;
    }

    public function getBalance(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['user_id'])) {
            return ['error' => 'Missing user_id', 'code' => 400];
        }

        $balance = $this->financeService->getBalance($data['user_id']);

        return ['success' => true, 'balance' => $balance];
    }

    public function deductBalance(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['user_id'], $data['amount'])) {
            return ['error' => 'Missing user_id or amount', 'code' => 400];
        }

        $result = $this->financeService->deductBalance($data['user_id'], $data['amount']);

        if (!$result) {
            return ['error' => 'Insufficient balance', 'code' => 402];
        }

        return ['success' => true, 'message' => 'Balance deducted'];
    }

    public function addBalance(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['user_id'], $data['amount'])) {
            return ['error' => 'Missing user_id or amount', 'code' => 400];
        }

        $this->financeService->addBalance($data['user_id'], $data['amount']);

        return ['success' => true, 'message' => 'Balance added'];
    }
}
