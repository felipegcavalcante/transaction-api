<?php

declare(strict_types=1);

namespace Tests\Infrastructure;

use Application\MakeTransfer;
use Application\TransactionOutputDto;
use DateTimeImmutable;
use Domain\Exceptions\TransactionException;
use Infrastructure\Http\Controllers\TransferController;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

use function json_encode;

class TransferControllerTest extends TestCase
{
    public function testShouldReturnErrorWhenValueIsNotFloat(): void
    {
        $expected = json_encode([
            'type' => 'ValidationError',
            'message' => 'Houve um erro ao validar o payload da requisição.',
            'errors' => [
                'value must be of type float',
            ],
        ]);

        $payload = [
            'value' => 100,
            'payer' => 1,
            'payee' => 2,
        ];

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn($payload);

        $makeTransfer = $this->createMock(MakeTransfer::class);

        $controller = new TransferController($makeTransfer);
        $response = $controller->handle($request);

        $this->assertSame($expected, $response->getBody()->getContents());
        $this->assertSame(422, $response->getStatusCode());
    }

    public function testShouldReturnErrorWhenPayerIsNotInt(): void
    {
        $expected = json_encode([
            'type' => 'ValidationError',
            'message' => 'Houve um erro ao validar o payload da requisição.',
            'errors' => [
                'payer must be of type integer',
            ],
        ]);

        $payload = [
            'value' => 100.0,
            'payer' => '100.0',
            'payee' => 100,
        ];

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn($payload);

        $makeTransfer = $this->createMock(MakeTransfer::class);

        $controller = new TransferController($makeTransfer);
        $response = $controller->handle($request);

        $this->assertSame($expected, $response->getBody()->getContents());
        $this->assertSame(422, $response->getStatusCode());
    }

    public function testShouldReturnErrorWhenPayeeIsNotInt(): void
    {
        $expected = json_encode([
            'type' => 'ValidationError',
            'message' => 'Houve um erro ao validar o payload da requisição.',
            'errors' => [
                'payee must be of type integer',
            ],
        ]);

        $payload = [
            'value' => 100.0,
            'payer' => 100,
            'payee' => true,
        ];

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn($payload);

        $makeTransfer = $this->createMock(MakeTransfer::class);

        $controller = new TransferController($makeTransfer);
        $response = $controller->handle($request);

        $this->assertSame($expected, $response->getBody()->getContents());
        $this->assertSame(422, $response->getStatusCode());
    }

    public function testShouldReturnErrorWhenDataIsEmpty(): void
    {
        $expected = json_encode([
            'type' => 'ValidationError',
            'message' => 'Houve um erro ao validar o payload da requisição.',
            'errors' => [
                'payer must be present',
                'payee must be present',
                'value must be present',
            ],
        ]);

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn([]);

        $makeTransfer = $this->createMock(MakeTransfer::class);

        $controller = new TransferController($makeTransfer);
        $response = $controller->handle($request);

        $this->assertSame($expected, $response->getBody()->getContents());
        $this->assertSame(422, $response->getStatusCode());
    }

    public function testShouldReturnErrorWhenMakeTransferThrowDomainException(): void
    {
        $expected = json_encode([
            'type' => 'InsufficientBalance',
            'message' => 'Usuário pagador não possui saldo suficiente para esta tranferência.',
        ]);

        $payload = ['value' => 100.0, 'payer' => 1, 'payee' => 2];

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn($payload);

        $makeTransfer = $this->createStub(MakeTransfer::class);
        $makeTransfer->method('handle')->willThrowException(TransactionException::forInsufficientBalance());

        $controller = new TransferController($makeTransfer);
        $response = $controller->handle($request);

        $this->assertSame($expected, $response->getBody()->getContents());
        $this->assertSame(400, $response->getStatusCode());
    }

    public function testShouldReturnSuccess(): void
    {
        $expected = new TransactionOutputDto(
            transactionId: 'uuid',
            value: 100.0,
            payer: 1,
            payee: 2,
            occurredAt: new DateTimeImmutable(),
        );

        $payload = ['value' => 100.0, 'payer' => 1, 'payee' => 2];

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn($payload);

        $makeTransfer = $this->createStub(MakeTransfer::class);
        $makeTransfer->method('handle')->willReturn($expected);

        $controller = new TransferController($makeTransfer);
        $response = $controller->handle($request);

        $this->assertSame(json_encode($expected->toArray()), $response->getBody()->getContents());
        $this->assertSame(201, $response->getStatusCode());
    }
}
