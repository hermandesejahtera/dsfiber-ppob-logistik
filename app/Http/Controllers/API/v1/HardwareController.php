<?php

namespace DSFiber\Http\Controllers\API\v1;

use DSFiber\Domains\Hardware\Services\DeviceService;

/**
 * Hardware Controller
 */
class HardwareController
{
    private DeviceService $deviceService;

    public function __construct(DeviceService $deviceService)
    {
        $this->deviceService = $deviceService;
    }

    public function registerDevice(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['user_id'], $data['device_type'], $data['device_token'], $data['expires_at'])) {
            return ['error' => 'Missing required fields', 'code' => 400];
        }

        return $this->deviceService->registerDeviceToken(
            (int) $data['user_id'],
            $data['device_type'],
            $data['device_token'],
            $data['expires_at']
        );
    }

    public function validateDevice(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['device_token'])) {
            return ['error' => 'Missing device_token', 'code' => 400];
        }

        $valid = $this->deviceService->validateToken($data['device_token']);
        return ['success' => $valid];
    }
}
