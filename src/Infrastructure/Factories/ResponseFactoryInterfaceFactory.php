<?php

declare(strict_types=1);

namespace Infrastructure\Factories;

use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Factory\Psr17\LaminasDiactorosPsr17Factory;

class ResponseFactoryInterfaceFactory
{
    public function __invoke(): ResponseFactoryInterface
    {
        return LaminasDiactorosPsr17Factory::getResponseFactory();
    }
}
