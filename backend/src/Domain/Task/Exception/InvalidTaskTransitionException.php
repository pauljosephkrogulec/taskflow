<?php

declare(strict_types=1);

namespace App\Domain\Task\Exception;

use App\Domain\Task\ValueObject\TaskStatus;

final class InvalidTaskTransitionException extends \DomainException
{
    public function __construct(TaskStatus $from, TaskStatus $to)
    {
        parent::__construct(sprintf(
            'Task cannot transition from "%s" to "%s".',
            $from->value,
            $to->value,
        ));
    }
}
