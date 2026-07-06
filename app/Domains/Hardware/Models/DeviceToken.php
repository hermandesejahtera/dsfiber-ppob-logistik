<?php

namespace DSFiber\Domains\Hardware\Models;

/**
 * Device token model for EDC and printer devices
 */
class DeviceToken
{
    public int $id;
    public int $user_id;
    public string $device_type;
    public string $device_token;
    public string $expires_at;
    public string $status;
    public ?string $created_at;
    public ?string $updated_at;

    public function __construct(int $user_id, string $device_type, string $device_token, string $expires_at)
    {
        $this->user_id = $user_id;
        $this->device_type = strtoupper($device_type);
        $this->device_token = $device_token;
        $this->expires_at = $expires_at;
        $this->status = 'ACTIVE';
    }
}
