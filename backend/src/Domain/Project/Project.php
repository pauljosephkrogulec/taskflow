<?php

declare(strict_types=1);

namespace App\Domain\Project;

use App\Domain\Project\Exception\NotProjectMemberException;
use App\Domain\Project\Exception\NotProjectOwnerException;
use App\Domain\Project\ValueObject\ProjectMemberRole;
use App\Domain\Shared\AggregateRoot;

class Project extends AggregateRoot
{
    /** @var ProjectMember[] */
    private array $members = [];
    private bool $archived = false;
    private \DateTimeImmutable $createdAt;

    public function __construct(
        private readonly string $id,
        private string $name,
        private readonly string $ownerId,
    ) {
        $this->createdAt = new \DateTimeImmutable();
        $this->members[] = new ProjectMember($ownerId, ProjectMemberRole::Owner);
    }

    public static function create(string $id, string $name, string $ownerId): self
    {
        return new self($id, $name, $ownerId);
    }

    public function id(): string { return $this->id; }
    public function name(): string { return $this->name; }
    public function ownerId(): string { return $this->ownerId; }
    public function isArchived(): bool { return $this->archived; }
    public function createdAt(): \DateTimeImmutable { return $this->createdAt; }

    /** @return ProjectMember[] */
    public function members(): array { return $this->members; }

    public function addMember(string $userId, ProjectMemberRole $role = ProjectMemberRole::Member): void
    {
        if ($this->hasMember($userId)) {
            return;
        }
        $this->members[] = new ProjectMember($userId, $role);
    }

    public function removeMember(string $requesterId, string $userId): void
    {
        $this->assertOwner($requesterId);

        if ($userId === $this->ownerId) {
            throw new \DomainException('Cannot remove the project owner.');
        }

        $this->members = array_values(
            array_filter($this->members, fn (ProjectMember $m) => $m->userId() !== $userId)
        );
    }

    public function hasMember(string $userId): bool
    {
        foreach ($this->members as $member) {
            if ($member->userId() === $userId) {
                return true;
            }
        }

        return false;
    }

    public function rename(string $name): void
    {
        $this->name = trim($name);
    }

    public function archive(string $requesterId): void
    {
        $this->assertOwner($requesterId);
        $this->archived = true;
    }

    public function delete(string $requesterId): void
    {
        $this->assertOwner($requesterId);
    }

    private function assertOwner(string $userId): void
    {
        if ($userId !== $this->ownerId) {
            throw new NotProjectOwnerException($userId, $this->id);
        }
    }

    public function assertMember(string $userId): void
    {
        if (!$this->hasMember($userId)) {
            throw new NotProjectMemberException($userId, $this->id);
        }
    }
}
