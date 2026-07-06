<?php
/**
 * Biteship Logistics Webhook Handler
 * Callback untuk notifikasi status pengiriman
 */

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/config/bootstrap.php';

header('Content-Type: application/json');

try {
    $webhookSecret = env('BITESHIP_WEBHOOK_SECRET', '');

    // Verify webhook signature
    $signature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';
    $payload = file_get_contents('php://input');
    $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);

    if ($signature !== $expectedSignature) {
        throw new Exception('Invalid webhook signature');
    }

    $data = json_decode($payload, true);

    // Log webhook
    $logFile = BASE_PATH . '/storage/logs/biteship_webhook.log';
    file_put_contents(
        $logFile,
        date('Y-m-d H:i:s') . ' | ' . json_encode($data) . PHP_EOL,
        FILE_APPEND
    );

    // TODO: Process webhook
    // - Update waybill status
    // - Trigger event listeners
    // - Notify customer

    http_response_code(200);
    echo json_encode(['status' => 'ok', 'message' => 'Webhook processed']);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
