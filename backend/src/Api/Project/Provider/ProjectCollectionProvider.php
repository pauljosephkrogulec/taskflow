<?php

declare(strict_types=1);

namespace App\Api\Project\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Project\Dto\ProjectOutput;
use App\Api\Project\Dto\ProjectOutputMapper;
use App\Domain\Project\ProjectRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @implements ProviderInterface<ProjectOutput>
 */
final class ProjectCollectionProvider implements ProviderInterface
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projects,
        private readonly Security $security,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $userId   = $this->security->getUser()->getUserIdentifier();
        // getUserIdentifier() returns email; we need the id from the user object
        $user     = $this->security->getUser();
        $projects = $this->projects->findByMember($user->id());

        return array_map(ProjectOutputMapper::fromDomain(...), $projects);
    }
}
