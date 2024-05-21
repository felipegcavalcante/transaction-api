<?php

declare(strict_types=1);

namespace Infrastructure\Repositories;

use Domain\Entities\User;
use Domain\Exceptions\UserException;
use Domain\Interfaces\DatabaseConnection;
use Domain\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        private readonly DatabaseConnection $connection
    ) {
    }

    public function findById(int $userId): User
    {
        /**
         * @var null|array{
         *     id: int, type: string, name: string, email: string, document: string, balance: float, password: string
         * } $data
         */
        $data = $this->connection->query('SELECT * FROM users WHERE id = ?', [$userId]);

        if ($data === null) {
            throw UserException::forNotFoundUserById($userId);
        }

        return new User(
            id: $data['id'],
            type: $data['type'],
            name: $data['name'],
            email: $data['email'],
            document: $data['document'],
            balance: $data['balance'],
            password: $data['password']
        );
    }

    public function persist(User $user): void
    {
        if ($user->getId() === null) {
            $sql = 'INSERT INTO users (name, document, email, balance, type, password)
            VALUES (:name, :document, :email, :balance, :type, :password);';

            $this->connection->persist(
                $sql,
                [
                    'name' => $user->getName(),
                    'document' => $user->getDocument(),
                    'email' => $user->getEmail(),
                    'balance' => $user->getBalance(),
                    'type' => $user->getType(),
                    'password' => $user->getPassword(),
                ],
            );

            return;
        }

        $this->connection->persist(
            'UPDATE users
            SET
                name = :name,
                document = :document,
                email = :email,
                balance = :balance,
                type = :type
            WHERE id = :id',
            [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'document' => $user->getDocument(),
                'email' => $user->getEmail(),
                'balance' => $user->getBalance(),
                'type' => $user->getType(),
            ],
        );
    }
}
