<?php

declare(strict_types=1);

namespace App\Domain\Task;

use App\Domain\Project\Project;
use App\Domain\Shared\AggregateRoot;
use App\Domain\Task\Exception\AssigneeNotProjectMemberException;
use App\Domain\Task\Exception\InvalidTaskTransitionException;
use App\Domain\Task\ValueObject\TaskStatus;

class Task extends AggregateRoot
{
    private TaskStatus $status;
    private ?string $assigneeId;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        private readonly string $id,
        private readonly string $projectId,
        private string $title,
        private ?string $description,
        private ?string $reporterId,
        private ?\DateTimeImmutable $dueDate,
    ) {
        $this->status     = TaskStatus::Todo;
        $this->assigneeId = null;
        $this->createdAt  = new \DateTimeImmutable();
        $this->updatedAt  = new \DateTimeImmutable();
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
