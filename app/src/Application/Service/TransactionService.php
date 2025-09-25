<?php

namespace App\Application\Service;

use App\Entity\Account;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use RuntimeException;

final class TransactionService
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function perform(User $user, string $accountId, string $type, int $amountCents): Transaction
    {
        if ($amountCents <= 0) {
            throw new InvalidArgumentException('Amount must be > 0 cents');
        }
        if ($type !== 'deposit' && $type !== 'withdraw') {
            throw new InvalidArgumentException('Invalid type');
        }

        $account = $this->em->getRepository(Account::class)->find($accountId);
        if (!$account) {
            throw new RuntimeException('Account not found');
        }

        if ($account->getUser()->getId() !== $user->getId()) {
            throw new RuntimeException('Forbidden');
        }

        return $this->em->wrapInTransaction(function () use ($account, $type, $amountCents): Transaction {
            $this->em->lock($account, LockMode::PESSIMISTIC_WRITE);

            $current = $account->getBalanceCents();
            $next = $current;

            if ($type === 'deposit') {
                $next = $current + $amountCents;
            } else {
                // Withdraw
                if ($current < $amountCents) {
                    throw new RuntimeException('Insufficient: You spend a lot of money!');
                }
                $next = $current - $amountCents;
            }

            $account->setBalanceCents($next);

            $this->em->persist($account);

            $tx = new Transaction($account, $type, $amountCents, $next);
            $this->em->persist($tx);

            return $tx;
        });
    }
}
