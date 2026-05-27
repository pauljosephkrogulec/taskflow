<?php

declare(strict_types=1);

namespace App\Api\Project\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Api\Project\Dto\ProjectInput;
use App\Api\Project\Dto\ProjectOutput;
use App\Api\Project\Dto\ProjectOutputMapper;
use App\Domain\Project\Exception\NotProjectOwnerException;
use App\Domain\Project\ProjectRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @implements ProcessorInterface<ProjectInput, ProjectOutput>
 */
final class UpdateProjectProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projects,
        private readonly Security $security,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ProjectOutput
    {
        $project = $this->projects->findById($uriVariables['id']);

        if ($project === null) {
            throw new NotFoundHttpException('Project not found.');
        }

        $user = $this->security->getUser();

        try {
            $project->rename($data->name);
        } catch (NotProjectOwnerException $e) {
            throw new AccessDeniedHttpException($e->getMessage());
        }

        $this->projects->save($project);

        return ProjectOutputMapper::fromDomain($project);
    }
}
