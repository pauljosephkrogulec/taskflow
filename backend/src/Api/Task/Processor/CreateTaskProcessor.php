<?php

declare(strict_types=1);

namespace App\Api\Task\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Api\Task\Dto\TaskInput;
use App\Api\Task\Dto\TaskOutput;
use App\Api\Task\Dto\TaskOutputMapper;
use App\Domain\Project\Exception\NotProjectMemberException;
use App\Domain\Project\ProjectRepositoryInterface;
use App\Domain\Task\Task;
use App\Domain\Task\TaskRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Uid\Uuid;

/**
 * @implements ProcessorInterface<TaskInput, TaskOutput>
 */
final class CreateTaskProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly TaskRepositoryInterface $tasks,
        private readonly ProjectRepositoryInterface $projects,
        private readonly Security $security,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): TaskOutput
    {
        $project = $this->projects->findById($uriVariables['projectId']);

        if ($project === null) {
            throw new NotFoundHttpException('Project not found.');
        }

        $user = $this->security->getUser();

        try {
            $project->assertMember($user->id());
        } catch (NotProjectMemberException $e) {
            throw new AccessDeniedHttpException($e->getMessage());
        }

        $dueDate = $data->dueDate !== null
            ? new \DateTimeImmutable($data->dueDate)
            : null;

        $task = Task::create(
            Uuid::v4()->toRfc4122(),
            $project->id(),
            $data->title,
            $data->description,
            $user->id(),
            $dueDate,
        );

        if ($data->assigneeId !== null) {
            try {
                $task->assign($data->assigneeId, $project);
            } catch (\DomainException $e) {
                throw new UnprocessableEntityHttpException($e->getMessage());
            }
        }

        $this->tasks->save($task);

        return TaskOutputMapper::fromDomain($task);
    }
}
