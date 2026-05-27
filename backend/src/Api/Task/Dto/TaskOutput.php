<?php

declare(strict_types=1);

namespace App\Api\Task\Dto;

final class TaskOutput
{
    /** @param CommentOutput[] $comments */
    public function __construct(
        public readonly string $id,
        public readonly string $projectId,
        public readonly string $title,
        public readonly ?string $description,
        public readonly string $status,
        public readonly ?string $assigneeId,
        public readonly ?string $reporterId,
        public readonly ?string $dueDate,
        public readonly string $createdAt,
        public readonly string $updatedAt,
        public readonly array $comments = [],
    ) {}
}
