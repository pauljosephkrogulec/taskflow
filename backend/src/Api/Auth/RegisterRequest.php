<?php

declare(strict_types=1);

namespace App\Api\Auth;

use Symfony\Component\Validator\Constraints as Assert;

final class RegisterRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        #[Assert\Length(max: 180)]
        public readonly string $email = '',

        #[Assert\NotBlank]
        #[Assert\Length(min: 8, max: 72)]
        public readonly string $password = '',

        #[Assert\NotBlank]
        #[Assert\Length(min: 2, max: 100)]
        public readonly string $name = '',
    ) {}
}
