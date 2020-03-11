<?php

namespace App\Repository;

use App\Entity\FeedbacksToAdmin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FeedbacksToAdmin|null find($id, $lockMode = null, $lockVersion = null)
 * @method FeedbacksToAdmin|null findOneBy(array $criteria, array $orderBy = null)
 * @method FeedbacksToAdmin[]    findAll()
 * @method FeedbacksToAdmin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeedbacksToAdminRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FeedbacksToAdmin::class);
    }

    // /**
    //  * @return FeedbacksToAdmin[] Returns an array of FeedbacksToAdmin objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FeedbacksToAdmin
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
