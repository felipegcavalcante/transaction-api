<?php

declare(strict_types=1);

namespace Domain\Exceptions;

use function sprintf;

final class UserException extends DomainError
{
    public static function forNotFoundUserById(int $userId): self
    {
        return new self(
            type: 'NotFoundUserById',
            message: sprintf('Usuário com o id %s não foi encontrado.', $userId),
            code: 404,
        );
    }

    public static function forInsufficientBalance(): self
    {
        return new self(
            type: 'InsufficientBalance',
            message: 'Usuário não possui saldo suficiente.',
            code: 400,
        );
    }
}
