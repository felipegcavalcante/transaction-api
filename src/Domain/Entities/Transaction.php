<?php

declare(strict_types=1);

namespace Domain\Entities;

use DateTimeImmutable;
use Domain\Exceptions\TransactionException;

class Transaction
{
    private float $value;

    public function __construct(
        private string $uuid,
        private int $payerId,
        private int $payeeId,
        float $value,
        private DateTimeImmutable $occurredAt
    ) {
        $this->ensurePositiveValue($value);

        $this->value = $value;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getPayerId(): int
    {
        return $this->payerId;
    }

    public function getPayeeId(): int
    {
        return $this->payeeId;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getOccurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    /** @return array{uuid: string, payer_id: int, payee_id: int, value: float, occurred_at: string} */
    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'payer_id' => $this->payerId,
            'payee_id' => $this->payeeId,
            'value' => $this->value,
            'occurred_at' => $this->occurredAt->format('Y-m-d H:i:s'),
        ];
    }

    private function ensurePositiveValue(float $value): void
    {
        if ($value <= 0) {
            throw TransactionException::forCannotTransferNonPositiveValue();
        }
    }
}
