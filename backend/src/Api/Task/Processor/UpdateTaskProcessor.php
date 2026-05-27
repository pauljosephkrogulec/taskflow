<?php

declare(strict_types=1);

namespace App\Api\Task\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Api\Task\Dto\TaskInput;
use App\Api\Task\Dto\TaskOutput;
use App\Api\Task\Dto\TaskOutputMapper;
use App\Domain\Project\ProjectRepositoryInterface;
use App\Domain\Task\TaskRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * @implements ProcessorInterface<TaskInput, TaskOutput>
 */
final class UpdateTaskProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly TaskRepositoryInterface $tasks,
        private readonly ProjectRepositoryInterface $projects,
        private readonly Security $security,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): TaskOutput
    {
        $task = $this->tasks->findById($uriVariables['id']);

        if ($task === null) {
            throw new NotFoundHttpException('Task not found.');
        }

        $dueDate = $data->dueDate !== null ? new \DateTimeImmutable($data->dueDate) : null;
        $task->update($data->title, $data->description, $dueDate);

        if ($data->assigneeId !== null) {
            $project = $this->projects->findById($task->projectId());
            try {
                $task->assign($data->assigneeId, $project);
            } catch (\DomainException $e) {
                throw new UnprocessableEntityHttpException($e->getMessage());
            }
        } else {
            $task->unassign();
        }

        $this->tasks->save($task);

        return TaskOutputMapper::fromDomain($task);
    }
}
