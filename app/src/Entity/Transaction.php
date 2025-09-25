<?php

namespace App\Entity;

use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\UniqueConstraint(name: 'idx_tx_account_created', columns: ['account_id', 'created_at'])]
class Transaction
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private readonly Uuid $id;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'account_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Account $account;

    // 'deposit' or 'withdraw'
    #[ORM\Column(length: 16)]
    private string $type;

    #[ORM\Column(type: 'integer')]
    private int $amountCents;

    #[ORM\Column(type: 'integer')]
    private int $balanceAfterCents;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    public function __construct(Account $account, string $type, int $amountCents, int $balanceAfterCents)
    {
        $this->id = Uuid::v7();
        $this->account = $account;
        $this->type = $type;
        $this->amountCents = $amountCents;
        $this->balanceAfterCents = $balanceAfterCents;
        $this->createdAt = new DateTimeImmutable('now', new DateTimeZone('UTC'));
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAmountCents(): int
    {
        return $this->amountCents;
    }

    public function getBalanceAfterCents(): int
    {
        return $this->balanceAfterCents;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
