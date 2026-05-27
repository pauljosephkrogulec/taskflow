<?php

declare(strict_types=1);

namespace App\Api\Task\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Task\Dto\TaskOutput;
use App\Api\Task\Dto\TaskOutputMapper;
use App\Domain\Comment\CommentRepositoryInterface;
use App\Domain\Task\TaskRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @implements ProviderInterface<TaskOutput>
 */
final class TaskItemProvider implements ProviderInterface
{
    public function __construct(
        private readonly TaskRepositoryInterface $tasks,
        private readonly CommentRepositoryInterface $comments,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): TaskOutput
    {
        $task = $this->tasks->findById($uriVariables['id']);

        if ($task === null) {
            throw new NotFoundHttpException('Task not found.');
        }

        $comments = $this->comments->findByTask($task->id());

        return TaskOutputMapper::fromDomain($task, $comments);
    }
}
