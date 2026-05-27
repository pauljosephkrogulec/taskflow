<?php

declare(strict_types=1);

namespace App\Domain\Task;

use App\Domain\Project\Project;
use App\Domain\Shared\AggregateRoot;
use App\Domain\Task\Exception\AssigneeNotProjectMemberException;
use App\Domain\Task\Exception\InvalidTaskTransitionException;
use App\Domain\Task\ValueObject\TaskStatus;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \App\Infrastructure\Doctrine\Repository\DoctrineTaskRepository::class)]
#[ORM\Table(name: 'tasks')]
class Task extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(name: 'project_id', type: 'string', length: 36)]
    private string $projectId;

    #[ORM\Column(name: 'title', type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    private ?string $description;

    #[ORM\Column(name: 'status', enumType: TaskStatus::class, length: 20)]
    private TaskStatus $status;

    #[ORM\Column(name: 'assignee_id', type: 'string', length: 36, nullable: true)]
    private ?string $assigneeId;

    #[ORM\Column(name: 'reporter_id', type: 'string', length: 36, nullable: true)]
    private ?string $reporterId;

    #[ORM\Column(name: 'due_date', type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $dueDate;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        string $id,
        string $projectId,
        string $title,
        ?string $description,
        ?string $reporterId,
        ?\DateTimeImmutable $dueDate,
    ) {
        $this->id          = $id;
        $this->projectId   = $projectId;
        $this->title       = $title;
        $this->description = $description;
        $this->reporterId  = $reporterId;
        $this->dueDate     = $dueDate;
        $this->status      = TaskStatus::Todo;
        $this->assigneeId  = null;
        $this->createdAt   = new \DateTimeImmutable();
        $this->updatedAt   = new \DateTimeImmutable();
    }

    public static function create(
        string $id,
        string $projectId,
        string $title,
        ?string $description = null,
        ?string $reporterId = null,
        ?\DateTimeImmutable $dueDate = null,
    ): self {
        return new self($id, $projectId, $title, $description, $reporterId, $dueDate);
    }

    public function id(): string { return $this->id; }
    public function projectId(): string { return $this->projectId; }
    public function title(): string { return $this->title; }
    public function description(): ?string { return $this->description; }
    public function status(): TaskStatus { return $this->status; }
    public function assigneeId(): ?string { return $this->assigneeId; }
    public function reporterId(): ?string { return $this->reporterId; }
    public function dueDate(): ?\DateTimeImmutable { return $this->dueDate; }
    public function createdAt(): \DateTimeImmutable { return $this->createdAt; }
    public function updatedAt(): \DateTimeImmutable { return $this->updatedAt; }

    public function transition(TaskStatus $nextStatus): void
    {
        if (!$this->status->canTransitionTo($nextStatus)) {
            throw new InvalidTaskTransitionException($this->status, $nextStatus);
        }
        $this->status    = $nextStatus;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function assign(string $userId, Project $project): void
    {
        if (!$project->hasMember($userId)) {
            throw new AssigneeNotProjectMemberException($userId, $project->id());
        }
        $this->assigneeId = $userId;
        $this->updatedAt  = new \DateTimeImmutable();
    }

    public function unassign(): void
    {
        $this->assigneeId = null;
        $this->updatedAt  = new \DateTimeImmutable();
    }

    public function update(string $title, ?string $description, ?\DateTimeImmutable $dueDate): void
    {
        $this->title       = trim($title);
        $this->description = $description;
        $this->dueDate     = $dueDate;
        $this->updatedAt   = new \DateTimeImmutable();
    }
}
