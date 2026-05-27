<?php

declare(strict_types=1);

namespace App\Api\Comment\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class CommentInput
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 5000)]
        public readonly string $content = '',
    ) {}
}
