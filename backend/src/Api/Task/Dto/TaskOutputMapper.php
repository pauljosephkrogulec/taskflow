<?php

declare(strict_types=1);

namespace App\Api\Task\Dto;

use App\Domain\Comment\Comment;
use App\Domain\Task\Task;

final class TaskOutputMapper
{
    /** @param Comment[] $comments */
    public static function fromDomain(Task $task, array $comments = []): TaskOutput
    {
        return new TaskOutput(
            id:          $task->id(),
            projectId:   $task->projectId(),
            title:       $task->title(),
            description: $task->description(),
            status:      $task->status()->value,
            assigneeId:  $task->assigneeId(),
            reporterId:  $task->reporterId(),
            dueDate:     $task->dueDate()?->format('Y-m-d'),
            createdAt:   $task->createdAt()->format(\DateTimeInterface::ATOM),
            updatedAt:   $task->updatedAt()->format(\DateTimeInterface::ATOM),
            comments:    array_map(
                static fn (Comment $c) => new CommentOutput(
                    $c->id(),
                    $c->authorId(),
                    $c->content(),
                    $c->createdAt()->format(\DateTimeInterface::ATOM),
                ),
                $comments,
            ),
        );
    }
}
