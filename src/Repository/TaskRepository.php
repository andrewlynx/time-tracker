<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\User;
use App\Service\PaginatorFactory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Pagerfanta\Pagerfanta;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * @param User $user
     * @param int $page
     *
     * @return Pagerfanta
     */
    public function getUsersTasks(User $user, int $page = 1): Pagerfanta
    {
        $qb = $this->createQueryBuilder('t')
            ->Where('t.user = :user')
            ->setParameter('user', $user);

        return PaginatorFactory::createPaginator($qb->getQuery(), $page);
    }

    /**
     * @param User $user
     * @param string $dateFrom
     * @param string $dateTo
     *
     * @return array
     */
    public function findByDateRange(User $user, string $dateFrom, string $dateTo): array
    {
        $qb = $this->createQueryBuilder('t')
            ->Where('t.user = :user')
            ->andWhere('t.date >= :dateFrom')
            ->andWhere('t.date <= :dateTo')
            ->orderBy('t.date')
            ->setParameters([
                'user' => $user,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
            ]);

        return $qb->getQuery()->getResult();
    }
}
