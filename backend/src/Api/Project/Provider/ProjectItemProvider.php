<?php

declare(strict_types=1);

namespace App\Api\Project\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Project\Dto\ProjectOutput;
use App\Api\Project\Dto\ProjectOutputMapper;
use App\Domain\Project\ProjectRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @implements ProviderInterface<ProjectOutput>
 */
final class ProjectItemProvider implements ProviderInterface
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projects,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ProjectOutput
    {
        $project = $this->projects->findById($uriVariables['id']);

        if ($project === null) {
            throw new NotFoundHttpException('Project not found.');
        }

        return ProjectOutputMapper::fromDomain($project);
    }
}
