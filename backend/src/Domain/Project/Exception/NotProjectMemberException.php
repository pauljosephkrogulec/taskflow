<?php

declare(strict_types=1);

namespace App\Domain\Project\Exception;

final class NotProjectMemberException extends \DomainException
{
    public function __construct(string $userId, string $projectId)
    {
        parent::__construct(sprintf('User "%s" is not a member of project "%s".', $userId, $projectId));
    }
}
