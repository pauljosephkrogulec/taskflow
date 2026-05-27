<?php

declare(strict_types=1);

namespace App\Api\Task\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class TaskInput
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public readonly string $title = '',

        public readonly ?string $description = null,

        #[Assert\Uuid]
        public readonly ?string $assigneeId = null,

        #[Assert\Date]
        public readonly ?string $dueDate = null,
    ) {}
}
