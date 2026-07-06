<?php

namespace DSFiber\Domains\Finance\Services;

use DSFiber\Domains\Finance\Repositories\PaymentRequestRepository;
use DSFiber\Domains\Finance\Repositories\WalletRepository;

/**
 * Payment request tracking service
 */
class PaymentRequestService
{
    private PaymentRequestRepository $paymentRequestRepo;
    private WalletRepository $walletRepo;

    public function __construct(PaymentRequestRepository $paymentRequestRepo, WalletRepository $walletRepo)
    {
        $this->paymentRequestRepo = $paymentRequestRepo;
        $this->walletRepo = $walletRepo;
    }

    public function createRequest(int $userId, int $walletId, float $amount, string $bankReference, ?string $notes = null): array
    {
        $paymentRequest = new \DSFiber\Domains\Finance\Models\PaymentRequest($userId, $walletId, $amount, $bankReference);
        $paymentRequest->notes = $notes;

        $requestId = $this->paymentRequestRepo->create($paymentRequest);

        return ['success' => true, 'payment_request_id' => $requestId];
    }

    public function getRequest(int $id): ?\DSFiber\Domains\Finance\Models\PaymentRequest
    {
        return $this->paymentRequestRepo->findById($id);
    }

    public function confirmRequest(int $id, ?string $notes = null): bool
    {
        return $this->paymentRequestRepo->updateStatus($id, 'CONFIRMED', $notes);
    }
}
