<?php

declare(strict_types=1);

namespace App\Domain\Comment;

interface CommentRepositoryInterface
{
    public function findById(string $id): ?Comment;

    /** @return Comment[] */
    public function findByTask(string $taskId): array;

    public function save(Comment $comment): void;
    public function remove(Comment $comment): void;
}
