<?php

namespace DSFiber\Http\Controllers\API\v1;

use DSFiber\Domains\Transactions\Services\TransactionService;

/**
 * Transaction Controller
 */
class TransactionController
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function createTransaction(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['user_id'], $data['product_id'])) {
            return ['error' => 'Missing user_id or product_id', 'code' => 400];
        }

        $result = $this->transactionService->createTransaction(
            $data['user_id'],
            $data['product_id']
        );

        return $result;
    }

    public function getTransactionDetails(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['transaction_id'])) {
            return ['error' => 'Missing transaction_id', 'code' => 400];
        }

        $transaction = $this->transactionService->getTransactionDetails($data['transaction_id']);

        if (!$transaction) {
            return ['error' => 'Transaction not found', 'code' => 404];
        }

        return ['success' => true, 'data' => $transaction];
    }
}
