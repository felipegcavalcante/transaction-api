<?php

declare(strict_types=1);

namespace Infrastructure\Http\Controllers;

use Application\MakeTransfer;
use Domain\Exceptions\DomainError;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;
use Throwable;

use function array_values;

class TransferController implements RequestHandlerInterface
{
    public function __construct(
        private readonly MakeTransfer $makeTransfer
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody();

        try {
            v::key('payer', v::intType()->notEmpty())
                ->key('payee', v::intType()->notEmpty())
                ->key('value', v::floatType()->notEmpty())
                ->assert($data);

            /** @var array{payer: int, payee: int, value: float} $data */
            $outputDto = $this->makeTransfer->handle($data['payer'], $data['payee'], $data['value']);
        } catch (NestedValidationException $exception) {
            return new JsonResponse([
                'type' => 'ValidationError',
                'message' => 'Houve um erro ao validar o payload da requisição.',
                'errors' => array_values($exception->getMessages()),
            ], 422);
        } catch (DomainError $exception) {
            return new JsonResponse([
                'type' => $exception->getType(),
                'message' => $exception->getMessage(),
            ], $exception->getCode());
        } catch (Throwable) {
            return new JsonResponse([
                'type' => 'InternalError',
                'message' => 'Houve um erro interno na aplicação.',
            ], 500);
        }

        return new JsonResponse($outputDto->toArray(), 201);
    }
}
