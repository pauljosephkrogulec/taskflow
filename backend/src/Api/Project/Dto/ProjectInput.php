<?php

declare(strict_types=1);

namespace App\Api\Project\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class ProjectInput
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 2, max: 150)]
        public readonly string $name = '',
    ) {}
}
