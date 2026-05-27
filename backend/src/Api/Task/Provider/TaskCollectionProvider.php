<?php

declare(strict_types=1);

namespace App\Api\Task\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Task\Dto\TaskOutput;
use App\Api\Task\Dto\TaskOutputMapper;
use App\Domain\Task\TaskRepositoryInterface;

/**
 * @implements ProviderInterface<TaskOutput>
 */
final class TaskCollectionProvider implements ProviderInterface
{
    public function __construct(
        private readonly TaskRepositoryInterface $tasks,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $tasks = $this->tasks->findByProject($uriVariables['projectId']);

        return array_map(fn ($t) => TaskOutputMapper::fromDomain($t), $tasks);
    }
}
