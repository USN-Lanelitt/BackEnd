<?php

namespace App\Repository;

use App\Entity\UserHasUserRights;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method UserHasUserRights|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserHasUserRights|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserHasUserRights[]    findAll()
 * @method UserHasUserRights[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserHasUserRightsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserHasUserRights::class);
    }

    // /**
    //  * @return UserHasUserRights[] Returns an array of UserHasUserRights objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserHasUserRights
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
