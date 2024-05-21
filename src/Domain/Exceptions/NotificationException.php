<?php

declare(strict_types=1);

namespace Domain\Exceptions;

final class NotificationException extends DomainError
{
    public static function forErrorDuringConnection(): self
    {
        return new self(
            type: 'ErrorDuringConnection',
            message: 'Houve um erro ao se conectar com o serviço de notificação.',
            code: 400
        );
    }

    public static function forErrorDuringNotification(): self
    {
        return new self(
            type: 'ErrorDuringNotification',
            message: 'Houve um erro ao notificar o usuário.',
            code: 400
        );
    }
}
