<?php

declare(strict_types=1);

namespace App\Api\Project\Dto;

final class ProjectOutput
{
    /** @param MemberOutput[] $members */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $ownerId,
        public readonly bool $archived,
        public readonly string $createdAt,
        public readonly array $members = [],
    ) {}
}
