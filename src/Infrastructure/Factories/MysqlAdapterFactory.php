<?php

declare(strict_types=1);

namespace Infrastructure\Factories;

use Infrastructure\Adapters\MysqlAdapter;
use Laminas\Config\Config;
use PDO;
use Psr\Container\ContainerInterface;

class MysqlAdapterFactory
{
    public function __invoke(ContainerInterface $container): MysqlAdapter
    {
        /** @var Config $config */
        $config = $container->get(Config::class);
        $database = $config->toArray()['config']['database'];

        $pdo = new PDO($database['dsn'], $database['username'], $database['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return new MysqlAdapter($pdo);
    }
}
