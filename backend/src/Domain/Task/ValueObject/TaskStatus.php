<?php

declare(strict_types=1);

namespace App\Domain\Task\ValueObject;

enum TaskStatus: string
{
    case Todo       = 'todo';
    case InProgress = 'in_progress';
    case Review     = 'review';
    case Done       = 'done';

    /** @return self[] */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Todo       => [self::InProgress],
            self::InProgress => [self::Review, self::Todo],
            self::Review     => [self::Done, self::InProgress],
            self::Done       => [],
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->allowedTransitions(), true);
    }
}
