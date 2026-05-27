<?php

declare(strict_types=1);

namespace App\Api\Project\Dto;

use App\Domain\Project\Project;

final class ProjectOutputMapper
{
    public static function fromDomain(Project $project): ProjectOutput
    {
        $members = array_map(
            static fn ($m) => new MemberOutput(
                $m->userId(),
                $m->role()->value,
                $m->joinedAt()->format(\DateTimeInterface::ATOM),
            ),
            $project->members(),
        );

        return new ProjectOutput(
            id:        $project->id(),
            name:      $project->name(),
            ownerId:   $project->ownerId(),
            archived:  $project->isArchived(),
            createdAt: $project->createdAt()->format(\DateTimeInterface::ATOM),
            members:   $members,
        );
    }
}
