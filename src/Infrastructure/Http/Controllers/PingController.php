<?php

declare(strict_types=1);

namespace Infrastructure\Http\Controllers;

use DateTime;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use const DATE_ATOM;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
final class PingController implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $date = (new DateTime())->format(DATE_ATOM);

        return new JsonResponse(['date' => $date]);
    }
}
