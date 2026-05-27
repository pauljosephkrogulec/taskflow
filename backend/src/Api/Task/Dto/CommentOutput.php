<?php

declare(strict_types=1);

namespace App\Api\Task\Dto;

final class CommentOutput
{
    public function __construct(
        public readonly string $id,
        public readonly string $authorId,
        public readonly string $content,
        public readonly string $createdAt,
    ) {}
}
