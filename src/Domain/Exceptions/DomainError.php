<?php

declare(strict_types=1);

namespace Domain\Exceptions;

use DomainException;
use Throwable;

abstract class DomainError extends DomainException
{
    protected string $type;

    public function __construct(string $type, string $message, int $code = 400, ?Throwable $previous = null)
    {
        $this->type = $type;
        parent::__construct($message, $code, $previous);
    }

    public function getType(): string
    {
        return $this->type;
    }
}
