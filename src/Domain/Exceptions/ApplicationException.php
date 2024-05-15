<?php

declare(strict_types=1);

namespace Domain\Exceptions;

use DomainException;
use Throwable;

abstract class ApplicationException extends DomainException
{
    public function __construct(string $message, int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
