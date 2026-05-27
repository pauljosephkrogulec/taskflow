<?php

declare(strict_types=1);

namespace App\Domain\Project;

interface ProjectRepositoryInterface
{
    public function findById(string $id): ?Project;

    /** @return Project[] */
    public function findByMember(string $userId): array;

    public function save(Project $project): void;
    public function remove(Project $project): void;
}
