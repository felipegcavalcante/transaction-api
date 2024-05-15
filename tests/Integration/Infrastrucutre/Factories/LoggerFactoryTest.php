<?php

declare(strict_types=1);

namespace Tests\Integration;

use Infrastructure\Factories\LoggerFactory;
use Monolog\Logger;
use Tests\TestCase;

/**
 * @covers Infrastructure\Factories\LoggerFactory
 */
final class LoggerFactoryTest extends TestCase
{
    /**
     * test default logger instance name.
     */
    public function testDefaultLoggerIntanceName(): void
    {
        $loggerFactory = new LoggerFactory();

        /** @var Logger */
        $logger = $loggerFactory();

        $this->assertSame('app', $logger->getName());
    }
}
