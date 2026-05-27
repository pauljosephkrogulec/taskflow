<?php

declare(strict_types=1);

namespace App\Domain\Project\ValueObject;

enum ProjectMemberRole: string
{
    case Owner  = 'owner';
    case Member = 'member';
    case Viewer = 'viewer';
}
