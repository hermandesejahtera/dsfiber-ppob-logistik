<?php

namespace DSFiber\Http\Controllers\API\v1;

use DSFiber\Domains\Finance\Services\WalletService;

/**
 * Finance Controller
 */
class FinanceController
{
    private WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function getBalance(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['user_id'])) {
            return ['error' => 'Missing user_id', 'code' => 400];
        }

        $walletType = $data['wallet_type'] ?? 'PPOB';
        $balance = $this->walletService->getWalletBalance((int) $data['user_id'], $walletType);

        return ['success' => true, 'data' => $balance];
    }

    public function deductBalance(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['user_id'], $data['amount'])) {
            return ['error' => 'Missing user_id or amount', 'code' => 400];
        }

        $walletType = $data['wallet_type'] ?? 'PPOB';
        $result = $this->walletService->deductBalance((int) $data['user_id'], $walletType, (float) $data['amount']);

        if (!$result) {
            return ['error' => 'Insufficient balance', 'code' => 402];
        }

        return ['success' => true, 'message' => 'Balance deducted'];
    }

    public function addBalance(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['user_id'], $data['amount'], $data['bank_reference'])) {
            return ['error' => 'Missing user_id, amount or bank_reference', 'code' => 400];
        }

        $walletType = $data['wallet_type'] ?? 'PPOB';
        $this->walletService->addBalance((int) $data['user_id'], $walletType, (float) $data['amount'], $data['bank_reference']);

        return ['success' => true, 'message' => 'Balance added'];
    }

    public function requestTopUp(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['user_id'], $data['amount'], $data['bank_reference'])) {
            return ['error' => 'Missing user_id, amount or bank_reference', 'code' => 400];
        }

        $walletType = $data['wallet_type'] ?? 'PPOB';
        $result = $this->walletService->createPaymentRequest(
            (int) $data['user_id'],
            $walletType,
            (float) $data['amount'],
            $data['bank_reference'],
            $data['notes'] ?? null
        );

        return $result;
    }
}
