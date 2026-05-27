<?php

declare(strict_types=1);

namespace App\Api\Task;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Api\Task\Dto\TaskInput;
use App\Api\Task\Dto\TaskOutput;
use App\Api\Task\Dto\TransitionInput;
use App\Api\Task\Processor\CreateTaskProcessor;
use App\Api\Task\Processor\TransitionTaskProcessor;
use App\Api\Task\Processor\UpdateTaskProcessor;
use App\Api\Task\Provider\TaskCollectionProvider;
use App\Api\Task\Provider\TaskItemProvider;

#[ApiResource(
    shortName: 'Task',
    operations: [
        new GetCollection(
            uriTemplate: '/projects/{projectId}/tasks',
            uriVariables: ['projectId'],
            provider:    TaskCollectionProvider::class,
        ),
        new Post(
            uriTemplate:  '/projects/{projectId}/tasks',
            uriVariables: ['projectId'],
            input:        TaskInput::class,
            processor:    CreateTaskProcessor::class,
        ),
        new Get(
            uriTemplate: '/tasks/{id}',
            provider:    TaskItemProvider::class,
        ),
        new Patch(
            uriTemplate: '/tasks/{id}',
            input:       TaskInput::class,
            provider:    TaskItemProvider::class,
            processor:   UpdateTaskProcessor::class,
        ),
        new Patch(
            uriTemplate: '/tasks/{id}/transition',
            input:       TransitionInput::class,
            provider:    TaskItemProvider::class,
            processor:   TransitionTaskProcessor::class,
        ),
    ],
    output: TaskOutput::class,
)]
final class TaskResource {}
