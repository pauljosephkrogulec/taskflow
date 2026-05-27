<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Project\Project;
use App\Domain\Project\ProjectRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 */
class DoctrineProjectRepository extends ServiceEntityRepository implements ProjectRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function findById(string $id): ?Project
    {
        return $this->find($id);
    }

    public function findByMember(string $userId): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.members', 'm')
            ->where('m.userId = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function save(Project $project): void
    {
        $this->getEntityManager()->persist($project);
        $this->getEntityManager()->flush();
    }

    public function remove(Project $project): void
    {
        $this->getEntityManager()->remove($project);
        $this->getEntityManager()->flush();
    }
}
