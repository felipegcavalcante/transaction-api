<?php

declare(strict_types=1);

use Slim\App;

use function Infrastructure\Adapters\Support\basePath;
use function Infrastructure\Adapters\Support\configPath;

require __DIR__ . '/../vendor/autoload.php';


$dotenv = Dotenv\Dotenv::createImmutable(basePath());
$dotenv->safeLoad();

// Build PHP-DI Container instance
$container = require configPath('container.php');

// Build Slim
$app = $container->get(App::class);

// Register middleware
$middleware = require configPath('middleware.php');
$middleware($app);

// Register routes
$routes = require configPath('routes.php');
$routes($app);

$app->run();
