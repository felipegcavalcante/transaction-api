<?php

declare(strict_types=1);

namespace Config;

use DI\ContainerBuilder;
use Domain\Interfaces\DatabaseConnection;
use Domain\Interfaces\TransactionRepositoryInterface;
use Domain\Interfaces\UserRepositoryInterface;
use Infrastructure\Factories\AppSlimFactory;
use Infrastructure\Factories\ConfigFactory;
use Infrastructure\Factories\HttpErrorFactory;
use Infrastructure\Factories\LoggerFactory;
use Infrastructure\Factories\MysqlAdapterFactory;
use Infrastructure\Factories\ResponseFactoryInterfaceFactory;
use Infrastructure\Factories\ServerRequestCreatorInterfaceFactory;
use Infrastructure\Handlers\HttpError;
use Infrastructure\Repositories\TransactionRepository;
use Infrastructure\Repositories\UserRepository;
use Laminas\Config\Config;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Interfaces\ServerRequestCreatorInterface;

use function DI\autowire;
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
    DatabaseConnection::class               => factory(MysqlAdapterFactory::class),
    UserRepositoryInterface::class          => autowire(UserRepository::class),
    TransactionRepositoryInterface::class   => autowire(TransactionRepository::class),
]);

// Should be set to true in production
if ($_ENV['APP_ENVIRONMENT'] === 'production') {
    $containerBuilder->enableCompilation(cachePath());
}

return $containerBuilder->build();
