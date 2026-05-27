<?php

declare(strict_types=1);

namespace App\Application\Auth;

use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use App\Domain\User\ValueObject\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class RegisterHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
        private readonly UserPasswordHasherInterface $hasher,
    ) {}

    public function handle(RegisterCommand $command): User
    {
        $email = new Email($command->email);

        if ($this->users->findByEmail($email->value()) !== null) {
            throw new \DomainException(sprintf('Email "%s" is already taken.', $email->value()));
        }

        $id   = \Symfony\Component\Uid\Uuid::v4()->toRfc4122();
        $user = User::register($id, $email, '', $command->name);

        $hash = $this->hasher->hashPassword($user, $command->password);
        $user->changePasswordHash($hash);

        $this->users->save($user);

        return $user;
    }
}
