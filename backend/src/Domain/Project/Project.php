<?php

declare(strict_types=1);

namespace App\Domain\Project;

use App\Domain\Project\Exception\NotProjectMemberException;
use App\Domain\Project\Exception\NotProjectOwnerException;
use App\Domain\Project\ValueObject\ProjectMemberRole;
use App\Domain\Shared\AggregateRoot;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \App\Infrastructure\Doctrine\Repository\DoctrineProjectRepository::class)]
#[ORM\Table(name: 'projects')]
class Project extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(name: 'name', type: 'string', length: 150)]
    private string $name;

    #[ORM\Column(name: 'owner_id', type: 'string', length: 36)]
    private string $ownerId;

    #[ORM\Column(name: 'archived', type: 'boolean', options: ['default' => false])]
    private bool $archived = false;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    /** @var Collection<int, ProjectMember> */
    #[ORM\OneToMany(targetEntity: ProjectMember::class, mappedBy: 'project', cascade: ['all'], orphanRemoval: true)]
    private Collection $members;

    public function __construct(
        string $id,
        string $name,
        string $ownerId,
    ) {
        $this->id        = $id;
        $this->name      = $name;
        $this->ownerId   = $ownerId;
        $this->createdAt = new \DateTimeImmutable();
        $this->members   = new ArrayCollection();
        $this->members->add(new ProjectMember($this, $ownerId, ProjectMemberRole::Owner));
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
    public function members(): array { return $this->members->toArray(); }

    public function addMember(string $userId, ProjectMemberRole $role = ProjectMemberRole::Member): void
    {
        if ($this->hasMember($userId)) {
            return;
        }
        $this->members->add(new ProjectMember($this, $userId, $role));
    }

    public function removeMember(string $requesterId, string $userId): void
    {
        $this->assertOwner($requesterId);

        if ($userId === $this->ownerId) {
            throw new \DomainException('Cannot remove the project owner.');
        }

        $toRemove = $this->members->filter(fn (ProjectMember $m) => $m->userId() === $userId);
        foreach ($toRemove as $member) {
            $this->members->removeElement($member);
        }
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

    public function assertMember(string $userId): void
    {
        if (!$this->hasMember($userId)) {
            throw new NotProjectMemberException($userId, $this->id);
        }
    }

    private function assertOwner(string $userId): void
    {
        if ($userId !== $this->ownerId) {
            throw new NotProjectOwnerException($userId, $this->id);
        }
    }
}
