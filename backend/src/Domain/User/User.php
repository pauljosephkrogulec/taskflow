<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\Shared\AggregateRoot;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\Role;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: \App\Infrastructure\Doctrine\Repository\DoctrineUserRepository::class)]
#[ORM\Table(name: 'users')]
class User extends AggregateRoot implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Embedded(class: Email::class, columnPrefix: false)]
    private Email $email;

    #[ORM\Column(name: 'password_hash', type: 'string', length: 255)]
    private string $passwordHash;

    #[ORM\Column(name: 'name', type: 'string', length: 100)]
    private string $name;

    #[ORM\Column(name: 'role', enumType: Role::class, length: 20)]
    private Role $role;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(
        string $id,
        Email $email,
        string $passwordHash,
        string $name,
        Role $role = Role::User,
    ) {
        $this->id           = $id;
        $this->email        = $email;
        $this->passwordHash = $passwordHash;
        $this->name         = $name;
        $this->role         = $role;
        $this->createdAt    = new \DateTimeImmutable();
    }

    public static function register(string $id, Email $email, string $passwordHash, string $name): self
    {
        return new self($id, $email, $passwordHash, $name);
    }

    public function id(): string { return $this->id; }
    public function email(): Email { return $this->email; }
    public function name(): string { return $this->name; }
    public function passwordHash(): string { return $this->passwordHash; }
    public function role(): Role { return $this->role; }
    public function createdAt(): \DateTimeImmutable { return $this->createdAt; }

    public function changeEmail(Email $email): void
    {
        $this->email = $email;
    }

    public function changeName(string $name): void
    {
        $this->name = trim($name);
    }

    public function changePasswordHash(string $hash): void
    {
        $this->passwordHash = $hash;
    }

    public function promoteToAdmin(): void
    {
        $this->role = Role::Admin;
    }

    // ── Symfony UserInterface ────────────────────────────────────────────────

    public function getUserIdentifier(): string
    {
        return $this->email->value();
    }

    /** @return string[] */
    public function getRoles(): array
    {
        return [$this->role->value];
    }

    public function getPassword(): ?string
    {
        return $this->passwordHash;
    }

    public function eraseCredentials(): void {}
}
