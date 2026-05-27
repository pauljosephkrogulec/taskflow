<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Domain\User\UserRepositoryInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @implements UserProviderInterface<\App\Domain\User\User>
 */
class UserProvider implements UserProviderInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
    ) {}

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->users->findByEmail($identifier);

        if ($user === null) {
            throw new UserNotFoundException(sprintf('User "%s" not found.', $identifier));
        }

        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return $class === \App\Domain\User\User::class || is_subclass_of($class, \App\Domain\User\User::class);
    }
}
