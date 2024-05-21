<?php

declare(strict_types=1);

namespace Application;

use DateTimeImmutable;

class TransactionOutputDto
{
    public function __construct(
        public readonly string $transactionId,
        public readonly float $value,
        public readonly int $payer,
        public readonly int $payee,
        public readonly DateTimeImmutable $occurredAt,
    ) {
    }

    /**
     * @return array{transaction_id: string, value: float, payer: int, payee: int, occurred_at: string}
     */
    public function toArray(): array
    {
        return [
            'transaction_id' => $this->transactionId,
            'value' => $this->value,
            'payer' => $this->payer,
            'payee' => $this->payee,
            'occurred_at' => $this->occurredAt->format('Y-m-d H:i:s'),
        ];
    }
}
