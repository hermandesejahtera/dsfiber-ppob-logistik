<?php

namespace DSFiber\Domains\Finance\Services;

use DSFiber\Domains\Finance\Models\Wallet;
use DSFiber\Domains\Finance\Repositories\WalletRepository;
use DSFiber\Domains\Finance\Repositories\PaymentRequestRepository;

/**
 * Wallet and payment request service
 */
class WalletService
{
    private WalletRepository $walletRepo;
    private PaymentRequestRepository $paymentRequestRepo;

    public function __construct(WalletRepository $walletRepo, PaymentRequestRepository $paymentRequestRepo)
    {
        $this->walletRepo = $walletRepo;
        $this->paymentRequestRepo = $paymentRequestRepo;
    }

    public function getWalletBalance(int $userId, string $walletType = 'PPOB'): array
    {
        $wallet = $this->walletRepo->createOrGet($userId, strtoupper($walletType));

        return [
            'user_id' => $wallet->user_id,
            'wallet_type' => $wallet->wallet_type,
            'balance' => $wallet->balance,
            'pending_balance' => $wallet->pending_balance,
            'hold_balance' => $wallet->hold_balance,
            'total_income' => $wallet->total_income,
            'total_expense' => $wallet->total_expense,
            'status' => $wallet->status,
        ];
    }

    public function addBalance(int $userId, string $walletType, float $amount, ?string $externalReference = null): bool
    {
        $wallet = $this->walletRepo->createOrGet($userId, strtoupper($walletType));
        $wallet->balance += $amount;
        $wallet->total_income += $amount;
        $wallet->external_reference = $externalReference ?? $wallet->external_reference;
        return $this->walletRepo->update($wallet);
    }

    public function deductBalance(int $userId, string $walletType, float $amount): bool
    {
        $wallet = $this->walletRepo->createOrGet($userId, strtoupper($walletType));

        if ($wallet->balance < $amount) {
            return false;
        }

        $wallet->balance -= $amount;
        $wallet->total_expense += $amount;
        return $this->walletRepo->update($wallet);
    }

    public function createPaymentRequest(int $userId, string $walletType, float $amount, string $bankReference, ?string $notes = null): array
    {
        $wallet = $this->walletRepo->createOrGet($userId, strtoupper($walletType));
        $paymentRequest = new \DSFiber\Domains\Finance\Models\PaymentRequest($userId, $wallet->id, $amount, $bankReference);
        $paymentRequest->notes = $notes;

        $requestId = $this->paymentRequestRepo->create($paymentRequest);
        return ['success' => true, 'payment_request_id' => $requestId];
    }

    public function getPaymentRequest(int $requestId): ?\DSFiber\Domains\Finance\Models\PaymentRequest
    {
        return $this->paymentRequestRepo->findById($requestId);
    }
}
