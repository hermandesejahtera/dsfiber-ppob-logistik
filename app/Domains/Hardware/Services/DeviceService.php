<?php

namespace DSFiber\Domains\Hardware\Services;

use DSFiber\Domains\Hardware\Models\DeviceToken;
use DSFiber\Domains\Hardware\Repositories\DeviceTokenRepository;

/**
 * Device token service for EDC and printer validity
 */
class DeviceService
{
    private DeviceTokenRepository $deviceRepo;

    public function __construct(DeviceTokenRepository $deviceRepo)
    {
        $this->deviceRepo = $deviceRepo;
    }

    public function registerDeviceToken(int $userId, string $deviceType, string $deviceToken, string $expiresAt): array
    {
        $device = new DeviceToken($userId, $deviceType, $deviceToken, $expiresAt);
        $id = $this->deviceRepo->create($device);

        return ['success' => true, 'device_id' => $id];
    }

    public function validateToken(string $token): bool
    {
        return $this->deviceRepo->findActiveByToken($token) !== null;
    }
}
