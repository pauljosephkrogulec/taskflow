<?php

declare(strict_types=1);

namespace App\Api\Project\Dto;

final class MemberOutput
{
    public function __construct(
        public readonly string $userId,
        public readonly string $role,
        public readonly string $joinedAt,
    ) {}
}
