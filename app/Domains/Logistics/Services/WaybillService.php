<?php

namespace DSFiber\Domains\Logistics\Services;

use DSFiber\Domains\Logistics\Models\Waybill;
use DSFiber\Domains\Logistics\Repositories\WaybillRepository;
use DSFiber\Infrastructure\Biteship\Client as BiteshipClient;

/**
 * Waybill and logistics tracking service
 */
class WaybillService
{
    private WaybillRepository $waybillRepo;
    private BiteshipClient $biteshipClient;

    public function __construct(WaybillRepository $waybillRepo, BiteshipClient $biteshipClient)
    {
        $this->waybillRepo = $waybillRepo;
        $this->biteshipClient = $biteshipClient;
    }

    public function createWaybill(array $data): array
    {
        $waybillNumber = $data['waybill_number'] ?? 'WB-' . time() . '-' . rand(1000, 9999);
        $waybill = new Waybill(
            (int) $data['user_id'],
            $waybillNumber,
            $data['courier_code'],
            $data['origin_address'],
            $data['destination_address'],
            $data['recipient_name'],
            $data['recipient_phone'],
            (int) $data['weight'],
            (float) $data['cost']
        );

        $waybill->tracking_status = [
            ['status' => 'CREATED', 'timestamp' => date('c'), 'note' => 'Waybill created'],
        ];

        $waybill->gateway_reference = $data['gateway_reference'] ?? null;
        $id = $this->waybillRepo->create($waybill);
        $waybill->id = $id;

        return ['success' => true, 'waybill' => $waybill];
    }

    public function getWaybillDetails(string $waybillNumber): ?Waybill
    {
        return $this->waybillRepo->findByWaybillNumber($waybillNumber);
    }

    public function updateTracking(string $waybillNumber, array $trackingEvent): bool
    {
        $waybill = $this->waybillRepo->findByWaybillNumber($waybillNumber);
        if (!$waybill) {
            return false;
        }

        $waybill->tracking_status[] = $trackingEvent;
        $waybill->status = $trackingEvent['status'] ?? $waybill->status;
        $waybill->delivered_at = $trackingEvent['status'] === 'DELIVERED' ? date('Y-m-d H:i:s') : $waybill->delivered_at;

        return $this->waybillRepo->update($waybill);
    }

    public function syncFromBiteship(string $waybillNumber): array
    {
        $response = $this->biteshipClient->getWaybill($waybillNumber);

        if (!isset($response['success']) || $response['success'] !== true) {
            return ['success' => false, 'message' => $response['message'] ?? 'Failed to sync'];
        }

        $details = $response['data'] ?? [];
        $tracking = $details['tracking_status'] ?? [];
        $status = $details['status'] ?? 'UNKNOWN';

        $waybill = $this->waybillRepo->findByWaybillNumber($waybillNumber);
        if (!$waybill) {
            return ['success' => false, 'message' => 'Waybill not found'];
        }

        $waybill->tracking_status = $tracking;
        $waybill->status = $status;
        $waybill->gateway_reference = $details['gateway_reference'] ?? $waybill->gateway_reference;
        $waybill->delivered_at = $details['delivered_at'] ?? $waybill->delivered_at;

        $this->waybillRepo->update($waybill);

        return ['success' => true, 'waybill' => $waybill];
    }
}
