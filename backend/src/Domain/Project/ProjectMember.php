<?php

declare(strict_types=1);

namespace App\Domain\Project;

use App\Domain\Project\ValueObject\ProjectMemberRole;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'project_members')]
class ProjectMember
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'members')]
    #[ORM\JoinColumn(name: 'project_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Project $project;

    #[ORM\Id]
    #[ORM\Column(name: 'user_id', type: 'string', length: 36)]
    private string $userId;

    #[ORM\Column(name: 'role', enumType: ProjectMemberRole::class, length: 20)]
    private ProjectMemberRole $role;

    #[ORM\Column(name: 'joined_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $joinedAt;

    public function __construct(
        Project $project,
        string $userId,
        ProjectMemberRole $role,
    ) {
        $this->project  = $project;
        $this->userId   = $userId;
        $this->role     = $role;
        $this->joinedAt = new \DateTimeImmutable();
    }

    public function project(): Project { return $this->project; }
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
