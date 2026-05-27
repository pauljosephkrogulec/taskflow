<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\Shared\AggregateRoot;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\Role;

class User extends AggregateRoot
{
    private \DateTimeImmutable $createdAt;

    public function __construct(
        private readonly string $id,
        private Email $email,
        private string $passwordHash,
        private string $name,
        private Role $role = Role::User,
    ) {
        $this->createdAt = new \DateTimeImmutable();
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

    public function promoteToAdmin(): void
    {
        $this->role = Role::Admin;
    }
}
