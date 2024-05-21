<?php

declare(strict_types=1);

namespace Tests\Application;

use Application\AuthorizationService;
use Application\MakeTransfer;
use Application\NotificationService;
use Domain\Entities\User;
use Domain\Exceptions\NotificationException;
use Domain\Exceptions\TransactionException;
use Domain\Exceptions\UserException;
use Domain\Interfaces\DatabaseConnection;
use Domain\Interfaces\TransactionRepositoryInterface;
use Domain\Interfaces\UserRepositoryInterface;
use PDOException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

class MakeTransferTest extends TestCase
{
    private TransactionRepositoryInterface&MockObject $transactionRepositoryMock;

    private UserRepositoryInterface&MockObject $userRepositoryMock;

    private DatabaseConnection&MockObject $connectionMock;

    private NotificationService&MockObject $notificationMock;

    private AuthorizationService&MockObject $authorizationMock;

    private LoggerInterface&MockObject $loggerMock;

    private MakeTransfer $makeTransfer;

    public function setUp(): void
    {
        $this->userRepositoryMock = $this->createMock(UserRepositoryInterface::class);
        $this->transactionRepositoryMock = $this->createMock(TransactionRepositoryInterface::class);
        $this->connectionMock = $this->createMock(DatabaseConnection::class);
        $this->authorizationMock = $this->createMock(AuthorizationService::class);
        $this->notificationMock = $this->createMock(NotificationService::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->makeTransfer = new MakeTransfer(
            $this->userRepositoryMock,
            $this->transactionRepositoryMock,
            $this->connectionMock,
            $this->authorizationMock,
            $this->notificationMock,
            $this->loggerMock,
        );
    }

    public function testShouldThrowAnExceptionWhenPayerNotFound(): void
    {
        $payerId = 1;

        $this->expectException(UserException::class);
        $this->expectExceptionMessage('Usuário com o id 1 não foi encontrado.');
        $this->expectExceptionCode(404);

        $this->userRepositoryMock
            ->method('findById')
            ->willThrowException(UserException::forNotFoundUserById($payerId));

        $this->makeTransfer->handle(payerId: $payerId, payeeId: 2, value: 10.0);
    }

    public function testShouldThrowAnExceptionWhenPayeeNotFound(): void
    {
        $payeeId = 1;

        $this->expectException(UserException::class);
        $this->expectExceptionMessage('Usuário com o id 1 não foi encontrado.');
        $this->expectExceptionCode(404);

        $this->userRepositoryMock
            ->method('findById')
            ->willThrowException(UserException::forNotFoundUserById($payeeId));

        $this->makeTransfer->handle(payerId: 2, payeeId: $payeeId, value: 10.0);
    }

    public function testShouldThrowAnExceptionWhenPayerAndPayeeAreTheSame(): void
    {
        $user = new User(
            id: 1,
            type: 'regular',
            name: 'John Doe',
            email: 'john.doe@example.com',
            document: '15665537098',
            balance: 100.0,
            password: 'p455w0rd',
        );

        $this->expectException(TransactionException::class);
        $this->expectExceptionMessage('Não é possível fazer uma transferência para o mesmo usuário.');
        $this->expectExceptionCode(400);

        $this->userRepositoryMock
            ->method('findById')
            ->willReturn($user);

        $this->makeTransfer->handle(payerId: 1, payeeId: 1, value: 10.0);
    }

    public function testShouldThrowAnExceptionWhenPayerCannotTransfer(): void
    {
        $payer = new User(
            id: 1,
            type: 'merchant',
            name: 'John Doe',
            email: 'john.doe@example.com',
            document: '15665537098',
            balance: 100.0,
            password: 'p455w0rd',
        );

        $payee = new User(
            id: 2,
            type: 'merchant',
            name: 'Mercados XYZ',
            email: 'mercado@example.com',
            document: '34495346000120',
            balance: 100.0,
            password: 'p455w0rd',
        );

        $this->expectException(TransactionException::class);
        $this->expectExceptionMessage('Usuários do tipo lojista não podem realizar transferências.');
        $this->expectExceptionCode(400);

        $this->userRepositoryMock
            ->method('findById')
            ->willReturnOnConsecutiveCalls($payer, $payee);

        $this->makeTransfer->handle(payerId: 1, payeeId: 2, value: 10.0);
    }

    public function testShouldThrowAnExceptionWhenPayerHasInsufficientBalance(): void
    {
        $payer = new User(
            id: 1,
            type: 'regular',
            name: 'John Doe',
            email: 'john.doe@example.com',
            document: '15665537098',
            balance: 100.0,
            password: 'p455w0rd',
        );

        $payee = new User(
            id: 2,
            type: 'merchant',
            name: 'Mercados XYZ',
            email: 'mercado@example.com',
            document: '34495346000120',
            balance: 100.0,
            password: 'p455w0rd',
        );

        $this->expectException(TransactionException::class);
        $this->expectExceptionMessage('Usuário pagador não possui saldo suficiente para esta tranferência.');
        $this->expectExceptionCode(400);

        $this->userRepositoryMock
            ->method('findById')
            ->willReturnOnConsecutiveCalls($payer, $payee);

        $this->makeTransfer->handle(payerId: 1, payeeId: 2, value: 1000.0);
    }

    public function testShouldThrowAnExceptionWhenAuthorizationServiceDoesNotAuthorize(): void
    {
        $payer = new User(
            id: 1,
            type: 'regular',
            name: 'John Doe',
            email: 'john.doe@example.com',
            document: '15665537098',
            balance: 100.0,
            password: 'p455w0rd',
        );

        $payee = new User(
            id: 2,
            type: 'merchant',
            name: 'Mercados XYZ',
            email: 'mercado@example.com',
            document: '34495346000120',
            balance: 100.0,
            password: 'p455w0rd',
        );

        $this->expectException(TransactionException::class);
        $this->expectExceptionMessage('Transferência não autorizada.');
        $this->expectExceptionCode(400);

        $this->userRepositoryMock
            ->method('findById')
            ->willReturnOnConsecutiveCalls($payer, $payee);

        $this->authorizationMock
            ->method('canMakeTransfer')
            ->willReturn(false);

        $this->makeTransfer->handle(payerId: 1, payeeId: 2, value: 10.0);
    }

    public function testShouldThrowAnExceptionWhenThereIsAnErrorInPersistence(): void
    {
        $payer = new User(
            id: 1,
            type: 'regular',
            name: 'John Doe',
            email: 'john.doe@example.com',
            document: '15665537098',
            balance: 100.0,
            password: 'p455w0rd',
        );

        $payee = new User(
            id: 2,
            type: 'merchant',
            name: 'Mercados XYZ',
            email: 'mercado@example.com',
            document: '34495346000120',
            balance: 100.0,
            password: 'p455w0rd',
        );

        $this->expectException(TransactionException::class);
        $this->expectExceptionMessage('Não foi possível realizar a transferência.');
        $this->expectExceptionCode(400);

        $this->userRepositoryMock
            ->method('findById')
            ->willReturnOnConsecutiveCalls($payer, $payee);

        $this->userRepositoryMock
            ->method('persist')
            ->willThrowException(new PDOException('could not find driver.', 500));

        $this->authorizationMock
            ->method('canMakeTransfer')
            ->willReturn(true);

        $this->transactionRepositoryMock
            ->method('nextUuid')
            ->willReturn((string)Uuid::uuid4());

        $this->loggerMock
            ->expects($this->once())
            ->method('error');

        $this->makeTransfer->handle(payerId: 1, payeeId: 2, value: 10.0);
    }

    public function testShouldCreateLogWhenNotificationServiceCannotNotify(): void
    {
        $payer = new User(
            id: 1,
            type: 'regular',
            name: 'John Doe',
            email: 'john.doe@example.com',
            document: '15665537098',
            balance: 100.0,
            password: 'p455w0rd',
        );

        $payee = new User(
            id: 2,
            type: 'merchant',
            name: 'Mercados XYZ',
            email: 'mercado@example.com',
            document: '34495346000120',
            balance: 100.0,
            password: 'p455w0rd',
        );

        $this->userRepositoryMock
            ->method('findById')
            ->willReturnOnConsecutiveCalls($payer, $payee);

        $this->authorizationMock
            ->method('canMakeTransfer')
            ->willReturn(true);

        $this->transactionRepositoryMock
            ->method('nextUuid')
            ->willReturn((string)Uuid::uuid4());

        $this->notificationMock
            ->method('notify')
            ->willThrowException(NotificationException::forErrorDuringNotification());

        $this->loggerMock
            ->expects($this->once())
            ->method('warning');

        $this->makeTransfer->handle(payerId: 1, payeeId: 2, value: 10.0);
    }

    public function testShouldReturnMakeTransferDtoWhenTransferOccurredWithSuccess(): void
    {
        $expected = [
            'payer' => 1,
            'payee' => 2,
            'value' => 10.0,
            'uuid' => (string) Uuid::uuid4(),
        ];

        $payer = new User(
            id: $expected['payer'],
            type: 'regular',
            name: 'John Doe',
            email: 'john.doe@example.com',
            document: '15665537098',
            balance: 100.0,
            password: 'p455w0rd',
        );

        $payee = new User(
            id: $expected['payee'],
            type: 'merchant',
            name: 'Mercados XYZ',
            email: 'mercado@example.com',
            document: '34495346000120',
            balance: 100.0,
            password: 'p455w0rd',
        );

        $this->userRepositoryMock
            ->method('findById')
            ->willReturnOnConsecutiveCalls($payer, $payee);

        $this->authorizationMock
            ->method('canMakeTransfer')
            ->willReturn(true);

        $this->transactionRepositoryMock
            ->method('nextUuid')
            ->willReturn($expected['uuid']);

        $outputDto = $this->makeTransfer->handle(payerId: 1, payeeId: 2, value: 10.0);

        $this->assertSame($expected['value'], $outputDto->value);
        $this->assertSame($expected['payer'], $outputDto->payer);
        $this->assertSame($expected['payee'], $outputDto->payee);
        $this->assertSame($expected['uuid'], $outputDto->transactionId);
    }
}
