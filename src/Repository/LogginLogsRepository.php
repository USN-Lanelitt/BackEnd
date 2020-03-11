<?php

namespace App\Repository;

use App\Entity\LogginLogs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method LogginLogs|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogginLogs|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogginLogs[]    findAll()
 * @method LogginLogs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogginLogsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogginLogs::class);
    }

    // /**
    //  * @return LogginLogs[] Returns an array of LogginLogs objects
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
    public function findOneBySomeField($value): ?LogginLogs
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
