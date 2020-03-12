<?php

namespace App\Repository;

use App\Entity\UnwantedBehaviorReports;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method UnwantedBehaviorReports|null find($id, $lockMode = null, $lockVersion = null)
 * @method UnwantedBehaviorReports|null findOneBy(array $criteria, array $orderBy = null)
 * @method UnwantedBehaviorReports[]    findAll()
 * @method UnwantedBehaviorReports[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UnwantedBehaviorReportsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UnwantedBehaviorReports::class);
    }

    // /**
    //  * @return UnwantedBehaviorReports[] Returns an array of UnwantedBehaviorReports objects
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
    public function findOneBySomeField($value): ?UnwantedBehaviorReports
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
