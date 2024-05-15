<?php

declare(strict_types=1);

namespace Infrastructure\Factories;

use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Interfaces\ServerRequestCreatorInterface;

class ServerRequestCreatorInterfaceFactory
{
    public function __invoke(): ServerRequestCreatorInterface
    {
        return ServerRequestCreatorFactory::create();
    }
}
