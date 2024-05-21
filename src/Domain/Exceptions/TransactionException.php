<?php

declare(strict_types=1);

namespace Domain\Exceptions;

final class TransactionException extends DomainError
{
    public static function forCannotTransferNonPositiveValue(): self
    {
        return new self(
            type: 'TransferNonPositiveValue',
            message: 'O valor da transferência deve ser positivo.',
            code: 400
        );
    }

    public static function forCannotTransferToSameUser(): self
    {
        return new self(
            type: 'CannotTransferToSameUser',
            message: 'Não é possível fazer uma transferência para o mesmo usuário.',
            code: 400,
        );
    }

    public static function forCannotTransferByMerchant(): self
    {
        return new self(
            type: 'CannotTransferByMerchant',
            message: 'Usuários do tipo lojista não podem realizar transferências.',
            code: 400,
        );
    }

    public static function forInsufficientBalance(): self
    {
        return new self(
            type: 'InsufficientBalance',
            message: 'Usuário pagador não possui saldo suficiente para esta tranferência.',
            code: 400,
        );
    }

    public static function forUnauthorizedTransfer(): self
    {
        return new self(
            type: 'UnauthorizedTransfer',
            message: 'Transferência não autorizada.',
            code: 400,
        );
    }

    public static function forUnexpectedError(): self
    {
        return new self(
            type: 'UnexpectedError',
            message: 'Não foi possível realizar a transferência.',
            code: 400,
        );
    }
}
