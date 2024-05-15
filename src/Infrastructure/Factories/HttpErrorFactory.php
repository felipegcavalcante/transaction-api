<?php

declare(strict_types=1);

namespace Infrastructure\Factories;

use Infrastructure\Handlers\HttpError;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Error\Renderers\JsonErrorRenderer;

class HttpErrorFactory
{
    public function __invoke(ContainerInterface $container): HttpError
    {
        /** @var App $app */
        $app = $container->get(App::class);

        /** @var LoggerInterface $logger */
        $logger = $container->get(LoggerInterface::class);

        $http = new HttpError(
            $app->getCallableResolver(),
            $app->getResponseFactory(),
            $logger
        );

        $http->setLogErrorRenderer(JsonErrorRenderer::class);

        return $http;
    }
}
