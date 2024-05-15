<?php

declare(strict_types=1);

namespace Tests\Integration;

use Infrastructure\Factories\AppSlimFactory;
use Slim\App;
use Slim\Interfaces\RequestHandlerInvocationStrategyInterface;
use Tests\TestCase;

use function Infrastructure\Adapters\Support\configPath;

/**
 * @covers Infrastructure\Factories\AppSlimFactory
 */
final class AppSlimFactoryTest extends TestCase
{
    /**
     * test default app settings
     */
    public function testDefaultAppSetting(): void
    {
        $container      = require configPath('container.php');
        $appSlimFactory = new AppSlimFactory();

        /** @var App */
        $app = $appSlimFactory($container);
        $routerCollector = $app->getRouteCollector();

        $this->assertInstanceOf(
            RequestHandlerInvocationStrategyInterface::class,
            $routerCollector->getDefaultInvocationStrategy()
        );
    }
}
