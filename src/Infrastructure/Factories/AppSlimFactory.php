<?php

declare(strict_types=1);

namespace Infrastructure\Factories;

use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Handlers\Strategies\RequestHandler;

class AppSlimFactory
{
    public function __invoke(ContainerInterface $container): App
    {
        AppFactory::setContainer($container);
        $app = AppFactory::create();
        $app
            ->getRouteCollector()
            ->setDefaultInvocationStrategy(new RequestHandler(true));

        return $app;
    }
}
