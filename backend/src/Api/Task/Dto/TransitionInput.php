<?php

declare(strict_types=1);

namespace App\Api\Task\Dto;

use App\Domain\Task\ValueObject\TaskStatus;
use Symfony\Component\Validator\Constraints as Assert;

final class TransitionInput
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Choice(choices: ['todo', 'in_progress', 'review', 'done'], message: 'Invalid status value.')]
        public readonly string $status = '',
    ) {}
}
