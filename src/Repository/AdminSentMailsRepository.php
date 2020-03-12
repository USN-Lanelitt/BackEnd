<?php

namespace App\Repository;

use App\Entity\AdminSentMails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method AdminSentMails|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdminSentMails|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdminSentMails[]    findAll()
 * @method AdminSentMails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdminSentMailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdminSentMails::class);
    }

    // /**
    //  * @return AdminSentMails[] Returns an array of AdminSentMails objects
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
    public function findOneBySomeField($value): ?AdminSentMails
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
