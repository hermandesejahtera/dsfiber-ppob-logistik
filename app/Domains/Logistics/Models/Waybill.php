<?php

namespace DSFiber\Domains\Logistics\Models;

/**
 * Waybill Model
 */
class Waybill
{
    public int $id;
    public string $waybill_number;
    public int $user_id;
    public string $courier_code;
    public string $origin_address;
    public string $destination_address;
    public string $recipient_name;
    public string $recipient_phone;
    public int $weight;
    public float $cost;
    public string $status;
    public array $tracking_status;
    public ?string $gateway_reference;
    public ?string $created_at;
    public ?string $updated_at;
    public ?string $delivered_at;

    public function __construct(
        int $user_id,
        string $waybill_number,
        string $courier_code,
        string $origin_address,
        string $destination_address,
        string $recipient_name,
        string $recipient_phone,
        int $weight,
        float $cost
    ) {
        $this->user_id = $user_id;
        $this->waybill_number = $waybill_number;
        $this->courier_code = $courier_code;
        $this->origin_address = $origin_address;
        $this->destination_address = $destination_address;
        $this->recipient_name = $recipient_name;
        $this->recipient_phone = $recipient_phone;
        $this->weight = $weight;
        $this->cost = $cost;
        $this->status = 'CREATED';
        $this->tracking_status = [];
        $this->gateway_reference = null;
    }
