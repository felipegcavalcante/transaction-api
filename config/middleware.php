<?php

declare(strict_types=1);

use Infrastructure\Handlers\HttpError;
use Slim\App;

return function (App $app) {
    $app->addBodyParsingMiddleware();
    $app->addRoutingMiddleware();
    $middleware = $app->addErrorMiddleware(true, false, false);

    if (is_null($app->getContainer())) {
        throw new RuntimeException('Dependency container not configured');
    }

    /** @var Callable $httpError */
    $httpError = $app->getContainer()->get(HttpError::class);
    $middleware->setDefaultErrorHandler($httpError);
};
