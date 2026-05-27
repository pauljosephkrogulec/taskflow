<?php

declare(strict_types=1);

namespace App\Domain\Project\Exception;

final class NotProjectOwnerException extends \DomainException
{
    public function __construct(string $userId, string $projectId)
    {
        parent::__construct(sprintf('User "%s" is not the owner of project "%s".', $userId, $projectId));
    }
}
