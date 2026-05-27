<?php

declare(strict_types=1);

namespace App\Api\Comment;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Api\Comment\Dto\CommentInput;
use App\Api\Comment\Dto\CommentOutput;
use App\Api\Comment\Processor\CreateCommentProcessor;

#[ApiResource(
    shortName: 'Comment',
    operations: [
        new Post(
            uriTemplate:  '/tasks/{taskId}/comments',
            uriVariables: ['taskId'],
            input:        CommentInput::class,
            processor:    CreateCommentProcessor::class,
        ),
    ],
    output: CommentOutput::class,
)]
final class CommentResource {}
