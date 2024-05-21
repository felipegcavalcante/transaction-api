<?php

declare(strict_types=1);

namespace Infrastructure\Repositories;

use Domain\Entities\Transaction;
use Domain\Interfaces\DatabaseConnection;
use Domain\Interfaces\TransactionRepositoryInterface;
use Ramsey\Uuid\Uuid;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function __construct(
        private readonly DatabaseConnection $connection
    ) {
    }

    public function persist(Transaction $transaction): void
    {
        $this->connection->persist(
            'INSERT INTO transactions (uuid, payer_id, payee_id, value, occurred_at)
            VALUES (:uuid, :payer_id, :payee_id, :value, :occurred_at);',
            [
                'uuid' => $transaction->getUuid(),
                'payer_id' => $transaction->getPayerId(),
                'payee_id' => $transaction->getPayeeId(),
                'value' => $transaction->getValue(),
                'occurred_at' => $transaction->getOccurredAt()->format('Y-m-d H:i:s'),
            ],
        );
    }

    public function nextUuid(): string
    {
        return (string) Uuid::uuid4();
    }
}
