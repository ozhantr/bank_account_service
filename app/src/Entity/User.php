<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTimeImmutable;
use DateTimeZone;
use Deprecated;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[UniqueEntity('email')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private readonly Uuid $id;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[ORM\Column(length: 180, unique: true)]
    private string $email;

    /** @var list<non-empty-string> */
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[Assert\NotBlank]
    #[Assert\Length(min: 8, max: 72)]
    #[ORM\Column]
    private string $password;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $updatedAt;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: Account::class, cascade: ['persist'])]
    private ?Account $account = null;

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): void
    {
        $this->account = $account;

        if ($account->getUser() !== $this) {
            $account->setUser($this);
        }
    }

    public function __construct()
    {
        $this->id = Uuid::v7();
        $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = mb_strtolower(mb_trim($email));

        return $this;
    }

    /** @return non-empty-string */
    public function getUserIdentifier(): string
    {
        if ($this->email === '') {
            throw new LogicException('User email is not set.');
        }

        return $this->email;
    }

    /** @return list<non-empty-string> */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        /** @var list<non-empty-string> $roles */
        $roles = array_values(array_unique($roles));

        return $roles;
    }

    /** @param list<non-empty-string> $roles */
    public function setRoles(array $roles): static
    {
        /** @var list<non-empty-string> $roles */
        $this->roles = array_values(array_unique($roles));

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new DateTimeImmutable('now', new DateTimeZone('UTC'));
    }
}
