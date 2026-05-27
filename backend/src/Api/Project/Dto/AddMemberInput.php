<?php

declare(strict_types=1);

namespace App\Api\Project\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class AddMemberInput
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public readonly string $userId = '',
    ) {}
}
