<?php

declare(strict_types=1);

namespace Tests\Integration;

use Infrastructure\Factories\ConfigFactory;
use Tests\TestCase;

use function Infrastructure\Adapters\Support\configPath;

/**
 * @covers Infrastructure\Factories\ConfigFactory
 */
final class ConfigFactoryTest extends TestCase
{
    /**
     * test default app settings
     */
    public function testDefaultAppSetting(): void
    {
        $config = require configPath('app.php');

        $configFactory = new ConfigFactory();

        $this->assertSame(['config' => $config], $configFactory()->toArray());
    }
}
