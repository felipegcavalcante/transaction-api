<?php

declare(strict_types=1);

namespace Domain\Interfaces;

interface DatabaseConnection
{
    /**
     * @param mixed[] $params
     * @return mixed[]
     */
    public function query(string $statement, array $params): ?array;

    /**
     * @param mixed[] $params
     */
    public function persist(string $statement, array $params): void;

    public function beginTransaction(): void;

    public function commitTransaction(): void;

    public function rollbackTransaction(): void;
}
