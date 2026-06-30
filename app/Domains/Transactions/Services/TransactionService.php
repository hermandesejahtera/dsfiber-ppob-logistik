<?php

namespace DSFiber\Domains\Transactions\Services;

use DSFiber\Domains\Transactions\Models\Transaction;
use DSFiber\Domains\Transactions\Repositories\TransactionRepository;
use DSFiber\Domains\Catalog\Repositories\ProductRepository;
use DSFiber\Infrastructure\Event\EventBus;

/**
 * Transaction Service
 */
class TransactionService
{
    private TransactionRepository $transactionRepo;
    private ProductRepository $productRepo;

    public function __construct(
        TransactionRepository $transactionRepo,
        ProductRepository $productRepo
    ) {
        $this->transactionRepo = $transactionRepo;
        $this->productRepo = $productRepo;
    }

    /**
     * Create transaction
     */
    public function createTransaction(int $userId, int $productId): array
    {
        $product = $this->productRepo->findById($productId);

        if (!$product) {
            return ['success' => false, 'message' => 'Product not found'];
        }

        $referenceNo = 'TXN-' . time() . '-' . rand(1000, 9999);
        $transaction = new Transaction($userId, $productId, $product->selling_price, $referenceNo);
        $transactionId = $this->transactionRepo->create($transaction);

        // Publish event
        EventBus::publish('transaction.created', [
            'transaction_id' => $transactionId,
            'user_id' => $userId,
            'product_id' => $productId
        ]);

        return ['success' => true, 'transaction_id' => $transactionId, 'reference_no' => $referenceNo];
    }

    /**
     * Update transaction status
     */
    public function updateTransactionStatus(int $transactionId, string $status): bool
    {
        $updated = $this->transactionRepo->updateStatus($transactionId, $status);

        if ($updated) {
            EventBus::publish('transaction.status_updated', [
                'transaction_id' => $transactionId,
                'status' => $status
            ]);
        }

        return $updated;
    }

    /**
     * Get transaction details
     */
    public function getTransactionDetails(int $transactionId): ?Transaction
    {
        return $this->transactionRepo->findById($transactionId);
    }
}
