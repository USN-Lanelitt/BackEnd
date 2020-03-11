<?php

namespace App\Repository;

use App\Entity\LoanImages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method LoanImages|null find($id, $lockMode = null, $lockVersion = null)
 * @method LoanImages|null findOneBy(array $criteria, array $orderBy = null)
 * @method LoanImages[]    findAll()
 * @method LoanImages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LoanImagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LoanImages::class);
    }

    // /**
    //  * @return LoanImages[] Returns an array of LoanImages objects
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
    public function findOneBySomeField($value): ?LoanImages
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
