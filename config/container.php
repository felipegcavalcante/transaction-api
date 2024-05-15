<?php

declare(strict_types=1);

namespace Config;

use DI\ContainerBuilder;
use Infrastructure\Factories\AppSlimFactory;
use Infrastructure\Factories\ConfigFactory;
use Infrastructure\Factories\HttpErrorFactory;
use Infrastructure\Factories\LoggerFactory;
use Infrastructure\Factories\ResponseFactoryInterfaceFactory;
use Infrastructure\Factories\ServerRequestCreatorInterfaceFactory;
use Infrastructure\Handlers\HttpError;
use Laminas\Config\Config;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Interfaces\ServerRequestCreatorInterface;

use function DI\factory;
use function Infrastructure\Adapters\Support\cachePath;

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions([
    LoggerInterface::class                  => factory(LoggerFactory::class),
    App::class                              => factory(AppSlimFactory::class),
    HttpError::class                        => factory(HttpErrorFactory::class),
    ResponseFactoryInterface::class         => factory(ResponseFactoryInterfaceFactory::class),
    ServerRequestCreatorInterface::class    => factory(ServerRequestCreatorInterfaceFactory::class),
    Config::class                           => factory(ConfigFactory::class),
]);

// Should be set to true in production
if ($_ENV['APP_ENVIRONMENT'] === 'production') {
    $containerBuilder->enableCompilation(cachePath());
}

return $containerBuilder->build();
