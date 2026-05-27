<?php

declare(strict_types=1);

namespace App\Api\Project\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Domain\Project\Exception\NotProjectOwnerException;
use App\Domain\Project\ProjectRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @implements ProcessorInterface<void, void>
 */
final class DeleteProjectProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projects,
        private readonly Security $security,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $project = $this->projects->findById($uriVariables['id']);

        if ($project === null) {
            throw new NotFoundHttpException('Project not found.');
        }

        $user = $this->security->getUser();

        try {
            $project->archive($user->id());
        } catch (NotProjectOwnerException $e) {
            throw new AccessDeniedHttpException($e->getMessage());
        }

        $this->projects->save($project);
    }
}
