<?php

namespace App\Data;

class SaleData
{
    public function __construct(
        public readonly ?int $userId,
        public readonly ?int $vehicleId,
        public readonly ?int $attendedBy,
        public readonly ?string $notes,
        public readonly array $items,
        public readonly array $payment,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            userId: isset($data['user_id']) ? (int) $data['user_id'] : null,
            vehicleId: isset($data['vehicle_id']) ? (int) $data['vehicle_id'] : null,
            attendedBy: isset($data['attended_by']) ? (int) $data['attended_by'] : null,
            notes: trim((string) ($data['notes'] ?? '')) ?: null,
            items: $data['items'] ?? [],
            payment: $data['payment'] ?? [],
        );
    }
}
