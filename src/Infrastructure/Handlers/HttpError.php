<?php

declare(strict_types=1);

namespace Infrastructure\Handlers;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpNotImplementedException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Handlers\ErrorHandler;

class HttpError extends ErrorHandler
{
    public const BAD_REQUEST                = 'BAD_REQUEST';
    public const INSUFFICIENT_PRIVILEGES    = 'INSUFFICIENT_PRIVILEGES';
    public const NOT_ALLOWED                = 'NOT_ALLOWED';
    public const NOT_IMPLEMENTED            = 'NOT_IMPLEMENTED';
    public const RESOURCE_NOT_FOUND         = 'RESOURCE_NOT_FOUND';
    public const SERVER_ERROR               = 'SERVER_ERROR';
    public const UNAUTHENTICATED            = 'UNAUTHENTICATED';

    protected function respond(): ResponseInterface
    {
        $exception      = $this->exception;
        $status         = 500;
        $type           = self::SERVER_ERROR;
        $description    = 'An internal error has occurred while processing your request.';

        if ($exception instanceof HttpException) {
            $status = $exception->getCode();
            $description = $exception->getMessage();
            $type = $this->getType($exception::class);
        }

        if ($this->displayErrorDetails) {
            $description = $exception->getMessage();
        }

        $error = [
            'statusCode' => $status,
            'error' => [
                'type' => $type,
                'description' => $description,
            ],
        ];

        return new JsonResponse($error, $status);
    }

    private function getType(string $type): string
    {
        $classes = [
            HttpBadRequestException::class          => self::BAD_REQUEST,
            HttpMethodNotAllowedException::class    => self::NOT_ALLOWED,
            HttpNotImplementedException::class      => self::NOT_IMPLEMENTED,
            HttpNotFoundException::class            => self::RESOURCE_NOT_FOUND,
            HttpInternalServerErrorException::class => self::SERVER_ERROR,
            HttpUnauthorizedException::class        => self::UNAUTHENTICATED,
        ];

        return $classes[$type];
    }
}
