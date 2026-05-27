<?php

declare(strict_types=1);

namespace App\Api\Task\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Api\Task\Dto\TaskOutput;
use App\Api\Task\Dto\TaskOutputMapper;
use App\Api\Task\Dto\TransitionInput;
use App\Domain\Task\Exception\InvalidTaskTransitionException;
use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\Task\ValueObject\TaskStatus;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * @implements ProcessorInterface<TransitionInput, TaskOutput>
 */
final class TransitionTaskProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly TaskRepositoryInterface $tasks,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): TaskOutput
    {
        $task = $this->tasks->findById($uriVariables['id']);

        if ($task === null) {
            throw new NotFoundHttpException('Task not found.');
        }

        $nextStatus = TaskStatus::from($data->status);

        try {
            $task->transition($nextStatus);
        } catch (InvalidTaskTransitionException $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }

        $this->tasks->save($task);

        return TaskOutputMapper::fromDomain($task);
    }
}
