<?php

namespace App\Repository;

use App\Entity\AdminMails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method AdminMails|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdminMails|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdminMails[]    findAll()
 * @method AdminMails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdminMailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdminMails::class);
    }

    // /**
    //  * @return AdminMails[] Returns an array of AdminMails objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AdminMails
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
