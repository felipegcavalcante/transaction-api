<?php

declare(strict_types=1);

namespace Domain\Interfaces;

use Domain\Entities\User;
use Domain\Exceptions\UserException;

interface UserRepositoryInterface
{
    /**
     * @throws UserException
     */
    public function findById(int $userId): User;

    public function persist(User $user): void;
}
