<?php

declare(strict_types=1);

namespace App\Domain\Task\Exception;

final class AssigneeNotProjectMemberException extends \DomainException
{
    public function __construct(string $userId, string $projectId)
    {
        parent::__construct(sprintf(
            'User "%s" cannot be assigned to a task because they are not a member of project "%s".',
            $userId,
            $projectId,
        ));
    }
}
