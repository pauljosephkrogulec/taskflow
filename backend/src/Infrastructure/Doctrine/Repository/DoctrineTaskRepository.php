<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Task\Task;
use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\Task\ValueObject\TaskStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
class DoctrineTaskRepository extends ServiceEntityRepository implements TaskRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function findById(string $id): ?Task
    {
        return $this->find($id);
    }

    public function findByProject(string $projectId): array
    {
        return $this->findBy(['projectId' => $projectId], ['createdAt' => 'ASC']);
    }

    public function findByAssignee(string $userId): array
    {
        return $this->findBy(['assigneeId' => $userId], ['updatedAt' => 'DESC']);
    }

    public function findByProjectAndStatus(string $projectId, TaskStatus $status): array
    {
        return $this->findBy(['projectId' => $projectId, 'status' => $status]);
    }

    public function save(Task $task): void
    {
        $this->getEntityManager()->persist($task);
        $this->getEntityManager()->flush();
    }

    public function remove(Task $task): void
    {
        $this->getEntityManager()->remove($task);
        $this->getEntityManager()->flush();
    }
}
