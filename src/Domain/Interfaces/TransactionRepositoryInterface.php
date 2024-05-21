<?php

declare(strict_types=1);

namespace Domain\Interfaces;

use Domain\Entities\Transaction;

interface TransactionRepositoryInterface
{
    public function persist(Transaction $transaction): void;

    public function nextUuid(): string;
}
