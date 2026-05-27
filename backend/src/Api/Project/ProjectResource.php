<?php

declare(strict_types=1);

namespace App\Api\Project;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Api\Project\Dto\ProjectInput;
use App\Api\Project\Dto\ProjectOutput;
use App\Api\Project\Processor\CreateProjectProcessor;
use App\Api\Project\Processor\DeleteProjectProcessor;
use App\Api\Project\Processor\UpdateProjectProcessor;
use App\Api\Project\Provider\ProjectCollectionProvider;
use App\Api\Project\Provider\ProjectItemProvider;

#[ApiResource(
    shortName: 'Project',
    operations: [
        new GetCollection(
            uriTemplate: '/projects',
            provider:    ProjectCollectionProvider::class,
        ),
        new Post(
            uriTemplate:  '/projects',
            input:        ProjectInput::class,
            processor:    CreateProjectProcessor::class,
        ),
        new Get(
            uriTemplate: '/projects/{id}',
            provider:    ProjectItemProvider::class,
        ),
        new Patch(
            uriTemplate: '/projects/{id}',
            input:       ProjectInput::class,
            provider:    ProjectItemProvider::class,
            processor:   UpdateProjectProcessor::class,
        ),
        new Delete(
            uriTemplate: '/projects/{id}',
            provider:    ProjectItemProvider::class,
            processor:   DeleteProjectProcessor::class,
        ),
    ],
    output: ProjectOutput::class,
)]
final class ProjectResource {}
