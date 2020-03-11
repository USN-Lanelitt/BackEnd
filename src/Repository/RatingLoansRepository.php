<?php

namespace App\Repository;

use App\Entity\RatingLoans;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method RatingLoans|null find($id, $lockMode = null, $lockVersion = null)
 * @method RatingLoans|null findOneBy(array $criteria, array $orderBy = null)
 * @method RatingLoans[]    findAll()
 * @method RatingLoans[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RatingLoansRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RatingLoans::class);
    }

    // /**
    //  * @return RatingLoans[] Returns an array of RatingLoans objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RatingLoans
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
