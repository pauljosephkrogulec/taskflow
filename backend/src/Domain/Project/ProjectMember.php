<?php

declare(strict_types=1);

namespace App\Domain\Project;

use App\Domain\Project\ValueObject\ProjectMemberRole;

class ProjectMember
{
    private \DateTimeImmutable $joinedAt;

    public function __construct(
        private readonly string $userId,
        private ProjectMemberRole $role,
    ) {
        $this->joinedAt = new \DateTimeImmutable();
    }

    public function userId(): string { return $this->userId; }
    public function role(): ProjectMemberRole { return $this->role; }
    public function joinedAt(): \DateTimeImmutable { return $this->joinedAt; }

    public function changeRole(ProjectMemberRole $role): void
    {
        if ($this->role === ProjectMemberRole::Owner) {
            throw new \DomainException('Cannot change the role of the project owner.');
        }
        $this->role = $role;
    }
}
