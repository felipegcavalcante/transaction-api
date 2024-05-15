<?php

declare(strict_types=1);

namespace tests\Unit\Domain\Exceptions;

use Domain\Exceptions\ApplicationException;
use Tests\TestCase;
use Throwable;

/**
 * @covers Domain\Exceptions\ApplicationException
 */
final class ApplicationExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function shouldThrowAnApplication(): void
    {

        $this->expectException(ApplicationException::class);
        $this->expectExceptionMessage('Not Found');
        $this->expectExceptionCode(404);

        throw new class ('Not Found', 404) extends ApplicationException
        {
            public function __construct(string $message, int $code = 404, ?Throwable $previous = null)
            {
                parent::__construct($message, $code, $previous);
            }
        };
    }
}
