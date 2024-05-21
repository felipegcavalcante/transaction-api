<?php

declare(strict_types=1);

namespace Infrastructure\Adapters;

use Domain\Interfaces\DatabaseConnection;
use PDO;

use function is_array;

class MysqlAdapter implements DatabaseConnection
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    /**
     * @inheritDoc
     */
    public function query(string $statement, array $params): ?array
    {
        $action = $this->pdo->prepare($statement);
        $action->execute($params);
        $result = $action->fetch();

        return is_array($result) ? $result : null;
    }

    /**
     * @inheritDoc
     */
    public function persist(string $statement, array $params): void
    {
        $result = $this->pdo->prepare($statement);

        $result->execute($params);
    }

    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commitTransaction(): void
    {
        $this->pdo->commit();
    }

    public function rollbackTransaction(): void
    {
        $this->pdo->rollBack();
    }
}
