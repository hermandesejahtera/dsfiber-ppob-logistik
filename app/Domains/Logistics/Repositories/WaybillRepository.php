<?php

namespace DSFiber\Domains\Logistics\Repositories;

use DSFiber\Domains\Logistics\Models\Waybill;
use DSFiber\Infrastructure\Database\Connection;

/**
 * Waybill Repository
 */
class WaybillRepository
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function create(Waybill $waybill): int
    {
        $trackingStatusJson = json_encode($waybill->tracking_status);

        $stmt = $this->db->prepare(
            'INSERT INTO waybills (waybill_number, user_id, courier_code, origin_address, destination_address, recipient_name, recipient_phone, weight, cost, status, tracking_status, gateway_reference) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param(
            'sisssssissss',
            $waybill->waybill_number,
            $waybill->user_id,
            $waybill->courier_code,
            $waybill->origin_address,
            $waybill->destination_address,
            $waybill->recipient_name,
            $waybill->recipient_phone,
            $waybill->weight,
            $waybill->cost,
            $waybill->status,
            $trackingStatusJson,
            $waybill->gateway_reference
        );
        $stmt->execute();
        return $this->db->getConnection()->insert_id;
    }

    public function findByWaybillNumber(string $waybillNumber): ?Waybill
    {
        $stmt = $this->db->prepare('SELECT * FROM waybills WHERE waybill_number = ? LIMIT 1');
        $stmt->bind_param('s', $waybillNumber);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (!$row) {
            return null;
        }

        return $this->mapRowToWaybill($row);
    }

    public function update(Waybill $waybill): bool
    {
        $trackingStatusJson = json_encode($waybill->tracking_status);

        $stmt = $this->db->prepare(
            'UPDATE waybills SET status = ?, tracking_status = ?, gateway_reference = ?, updated_at = NOW(), delivered_at = ? WHERE waybill_number = ?'
        );
        $stmt->bind_param(
            'sssss',
            $waybill->status,
            $trackingStatusJson,
            $waybill->gateway_reference,
            $waybill->delivered_at,
            $waybill->waybill_number
        );

        return $stmt->execute();
    }

    private function mapRowToWaybill(array $row): Waybill
    {
        $waybill = new Waybill(
            (int) $row['user_id'],
            $row['waybill_number'],
            $row['courier_code'],
            $row['origin_address'],
            $row['destination_address'],
            $row['recipient_name'],
            $row['recipient_phone'],
            (int) $row['weight'],
            (float) $row['cost']
        );

        $waybill->id = (int) $row['id'];
        $waybill->status = $row['status'];
        $waybill->tracking_status = json_decode($row['tracking_status'] ?? '[]', true) ?: [];
        $waybill->gateway_reference = $row['gateway_reference'] ?? null;
        $waybill->created_at = $row['created_at'] ?? null;
        $waybill->updated_at = $row['updated_at'] ?? null;
        $waybill->delivered_at = $row['delivered_at'] ?? null;

        return $waybill;
    }
}
