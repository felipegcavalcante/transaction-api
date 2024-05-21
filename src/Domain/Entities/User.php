<?php

declare(strict_types=1);

namespace Domain\Entities;

use Domain\Exceptions\UserException;

final class User
{
    public function __construct(
        private ?int $id,
        private string $type,
        private string $name,
        private string $email,
        private string $document,
        private float $balance,
        private string $password
    ) {
    }

    public function debit(float $value): void
    {
        if (!$this->hasBalance($value)) {
            throw UserException::forInsufficientBalance();
        }

        $this->balance -= $value;
    }

    public function credit(float $value): void
    {
        $this->balance += $value;
    }

    public function canTransfer(): bool
    {
        return $this->type !== 'merchant';
    }

    public function hasBalance(float $value): bool
    {
        return $this->balance >= $value;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getDocument(): string
    {
        return $this->document;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
