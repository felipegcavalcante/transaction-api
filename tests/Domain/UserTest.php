<?php

declare(strict_types=1);

namespace Tests\Domain;

use Domain\Entities\User;
use Domain\Exceptions\UserException;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testShouldCreateAnUser(): void
    {
        $expected = [
            'id' => 1,
            'type' => 'regular',
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'document' => '15665537098',
            'balance' => 100.0,
            'password' => 'p455w0rd',
        ];

        $user = new User(...$expected);

        $this->assertSame($expected['id'], $user->getId());
        $this->assertSame($expected['type'], $user->getType());
        $this->assertSame($expected['name'], $user->getName());
        $this->assertSame($expected['email'], $user->getEmail());
        $this->assertSame($expected['document'], $user->getDocument());
        $this->assertSame($expected['balance'], $user->getBalance());
        $this->assertSame($expected['password'], $user->getPassword());
    }

    public function testShouldDebitBalanceOfUser(): void
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

        $user->debit(90.0);

        $this->assertSame(10.0, $user->getBalance());
    }

    public function testShouldThrowAnExceptionWhenDebitValueIsGreaterThanBalance(): void
    {
        $this->expectException(UserException::class);
        $this->expectExceptionMessage('Usuário não possui saldo suficiente.');
        $this->expectExceptionCode(400);

        $user = new User(
            id: 1,
            type: 'regular',
            name: 'John Doe',
            email: 'john.doe@example.com',
            document: '15665537098',
            balance: 100.0,
            password: 'p455w0rd',
        );

        $user->debit(1000.0);
    }

    public function testShouldCreditBalanceOfUser(): void
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

        $user->credit(10.0);

        $this->assertSame(110.0, $user->getBalance());
    }

    public function testShouldHasBalanceMethodReturnTrueToSufficientBalance(): void
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

        $this->assertTrue($user->hasBalance(10.0));
    }

    public function testShouldHasBalanceMethodReturnTrueToEqualBalance(): void
    {
        $user = new User(1, 'regular', 'John Doe', 'john.doe@example.com', '15665537098', 100.0, 'p455w0rd');

        $this->assertTrue($user->hasBalance(100.0));
    }

    public function testShouldHasBalanceMethodReturnFalseToInsufficientBalance(): void
    {
        $user = new User(1, 'regular', 'John Doe', 'john.doe@example.com', '15665537098', 100.0, 'p455w0rd');

        $this->assertFalse($user->hasBalance(1000.0));
    }

    public function testShouldCanTransferMethodReturnFalseToMerchantUser(): void
    {
        $user = new User(1, 'merchant', 'John Doe', 'john.doe@example.com', '15665537098', 100.0, 'p455w0rd');

        $this->assertFalse($user->canTransfer());
    }

    public function testShouldCanTransferMethodReturnTrueToRegularUser(): void
    {
        $user = new User(1, 'regular', 'John Doe', 'john.doe@example.com', '15665537098', 100.0, 'p455w0rd');

        $this->assertTrue($user->canTransfer());
    }
}
