<?php

namespace DSFiber\Http\Controllers\API\v1;

use DSFiber\Domains\Logistics\Services\WaybillService;

/**
 * Logistics Controller
 */
class LogisticsController
{
    private WaybillService $waybillService;

    public function __construct(WaybillService $waybillService)
    {
        $this->waybillService = $waybillService;
    }

    public function createWaybill(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $required = ['user_id', 'courier_code', 'origin_address', 'destination_address', 'recipient_name', 'recipient_phone', 'weight', 'cost'];

        foreach ($required as $field) {
            if (!isset($data[$field])) {
                return ['error' => "Missing {$field}", 'code' => 400];
            }
        }

        return $this->waybillService->createWaybill($data);
    }

    public function getWaybill(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['waybill_number'])) {
            return ['error' => 'Missing waybill_number', 'code' => 400];
        }

        $waybill = $this->waybillService->getWaybillDetails($data['waybill_number']);

        if (!$waybill) {
            return ['error' => 'Waybill not found', 'code' => 404];
        }

        return ['success' => true, 'data' => $waybill];
    }

    public function syncWaybill(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['waybill_number'])) {
            return ['error' => 'Missing waybill_number', 'code' => 400];
        }

        return $this->waybillService->syncFromBiteship($data['waybill_number']);
    }
}
