<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObject;

enum Role: string
{
    case User  = 'ROLE_USER';
    case Admin = 'ROLE_ADMIN';

    public function isAdmin(): bool
    {
        return $this === self::Admin;
    }
}
