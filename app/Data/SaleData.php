<?php

namespace App\Data;

class SaleData
{
    public function __construct(
        public readonly ?int $userId,
        public readonly ?int $attendedBy,
        public readonly string $scheduledAt,
        public readonly ?string $notes,
        public readonly array $items,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            userId: isset($data['user_id']) ? (int) $data['user_id'] : null,
            attendedBy: isset($data['attended_by']) ? (int) $data['attended_by'] : null,
            scheduledAt: (string) $data['scheduled_at'],
            notes: trim((string) ($data['notes'] ?? '')) ?: null,
            items: $data['items'] ?? [],
        );
    }
}
