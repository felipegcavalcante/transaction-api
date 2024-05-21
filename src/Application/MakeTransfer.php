<?php

declare(strict_types=1);

namespace Application;

use DateTimeImmutable;
use Domain\Entities\Transaction;
use Domain\Exceptions\DomainError;
use Domain\Exceptions\TransactionException;
use Domain\Interfaces\DatabaseConnection;
use Domain\Interfaces\TransactionRepositoryInterface;
use Domain\Interfaces\UserRepositoryInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class MakeTransfer
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly TransactionRepositoryInterface $transactionRepository,
        private readonly DatabaseConnection $connection,
        private readonly AuthorizationService $authorizationService,
        private readonly NotificationService $notificationService,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @throws DomainError
     */
    public function handle(int $payerId, int $payeeId, float $value): TransactionOutputDto
    {
        $payer = $this->userRepository->findById($payerId);
        $payee = $this->userRepository->findById($payeeId);

        if ($payerId === $payeeId) {
            throw TransactionException::forCannotTransferToSameUser();
        }

        if (!$payer->canTransfer()) {
            throw TransactionException::forCannotTransferByMerchant();
        }

        if (!$payer->hasBalance($value)) {
            throw TransactionException::forInsufficientBalance();
        }

        if (!$this->authorizationService->canMakeTransfer($payerId, $payeeId, $value)) {
            throw TransactionException::forUnauthorizedTransfer();
        }

        $uuid = $this->transactionRepository->nextUuid();

        $transaction = new Transaction($uuid, $payerId, $payeeId, $value, new DateTimeImmutable());

        try {
            $this->connection->beginTransaction();

            $payer->debit($value);
            $payee->credit($value);

            $this->userRepository->persist($payer);
            $this->userRepository->persist($payee);

            $this->transactionRepository->persist($transaction);

            $this->connection->commitTransaction();
        } catch (Throwable) {
            $this->connection->rollbackTransaction();

            $this->logger->error('Erro ao persistir transação.', $transaction->toArray());

            throw TransactionException::forUnexpectedError();
        }

        try {
            $this->notificationService->notify($transaction);
        } catch (Throwable) {
            $this->logger->warning('Erro ao enviar notificação ao usuário recebedor.', $transaction->toArray());
        }

        return new TransactionOutputDto(
            transactionId: $transaction->getUuid(),
            value: $value,
            payer: $payerId,
            payee: $payeeId,
            occurredAt: $transaction->getOccurredAt(),
        );
    }
}
