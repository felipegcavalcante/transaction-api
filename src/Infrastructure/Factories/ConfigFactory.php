<?php

declare(strict_types=1);

namespace Infrastructure\Factories;

use Laminas\Config\Config;

use function Infrastructure\Adapters\Support\configPath;

final class ConfigFactory
{
    public function __invoke(): Config
    {
        $config = require configPath('app.php');

        return new Config(['config' => $config]);
    }
}
