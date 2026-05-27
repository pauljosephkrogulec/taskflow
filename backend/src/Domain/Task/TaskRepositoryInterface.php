<?php

declare(strict_types=1);

namespace App\Domain\Task;

use App\Domain\Task\ValueObject\TaskStatus;

interface TaskRepositoryInterface
{
    public function findById(string $id): ?Task;

    /** @return Task[] */
    public function findByProject(string $projectId): array;

    /** @return Task[] */
    public function findByAssignee(string $userId): array;

    /** @return Task[] */
    public function findByProjectAndStatus(string $projectId, TaskStatus $status): array;

    public function save(Task $task): void;
    public function remove(Task $task): void;
}
