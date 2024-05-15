<?php

declare(strict_types=1);

namespace Infrastructure\Adapters\Support;

use function is_file;
use function is_null;
use function sprintf;

use const DIRECTORY_SEPARATOR;

function basePath(?string $pathOrFile = null): string
{
    $basePath = __DIR__ . '/../../../../';

    if (is_null($pathOrFile)) {
        return $basePath;
    }

    if (is_file($basePath . $pathOrFile)) {
        return $basePath . $pathOrFile;
    }

    return $basePath . $pathOrFile . DIRECTORY_SEPARATOR;
}

function configPath(?string $pathOrFile = null): string
{
    if (is_null($pathOrFile)) {
        basePath('config');
    }

    return basePath(sprintf('config/%s', $pathOrFile));
}

function varPath(?string $pathOrFile = null): string
{
    if (is_null($pathOrFile)) {
        basePath('var');
    }

    return basePath(sprintf('var/%s', $pathOrFile));
}

function cachePath(?string $pathOrFile = null): string
{
    if (is_null($pathOrFile)) {
        varPath('cache');
    }

    return varPath(sprintf('cache/%s', $pathOrFile));
}
