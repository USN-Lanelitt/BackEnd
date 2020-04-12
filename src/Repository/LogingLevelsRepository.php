<?php

namespace App\Repository;

use App\Entity\LogingLevels;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method LogingLevels|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogingLevels|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogingLevels[]    findAll()
 * @method LogingLevels[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogingLevelsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogingLevels::class);
    }

    // /**
    //  * @return LogingLevels[] Returns an array of LogingLevels objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LogingLevels
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
