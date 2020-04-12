<?php

namespace App\Repository;

use App\Entity\Variables;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Variables|null find($id, $lockMode = null, $lockVersion = null)
 * @method Variables|null findOneBy(array $criteria, array $orderBy = null)
 * @method Variables[]    findAll()
 * @method Variables[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VariablesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Variables::class);
    }

    // /**
    //  * @return Variables[] Returns an array of Variables objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Variables
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
