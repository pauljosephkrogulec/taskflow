<?php

declare(strict_types=1);

namespace App\Api\Project\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Api\Project\Dto\ProjectInput;
use App\Api\Project\Dto\ProjectOutput;
use App\Api\Project\Dto\ProjectOutputMapper;
use App\Domain\Project\Project;
use App\Domain\Project\ProjectRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Uid\Uuid;

/**
 * @implements ProcessorInterface<ProjectInput, ProjectOutput>
 */
final class CreateProjectProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projects,
        private readonly Security $security,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ProjectOutput
    {
        $user    = $this->security->getUser();
        $project = Project::create(
            Uuid::v4()->toRfc4122(),
            $data->name,
            $user->id(),
        );

        $this->projects->save($project);

        return ProjectOutputMapper::fromDomain($project);
    }
}
