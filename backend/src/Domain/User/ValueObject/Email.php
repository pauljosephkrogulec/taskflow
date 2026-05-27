<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObject;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class Email
{
    #[ORM\Column(name: 'email', type: 'string', length: 180, unique: true)]
    private string $value;

    public function __construct(string $value)
    {
        $value = mb_strtolower(trim($value));

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid email address.', $value));
        }

        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
