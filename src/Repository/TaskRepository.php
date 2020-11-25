<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Pagerfanta\Pagerfanta;
use PaginatorFactory;

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
}
