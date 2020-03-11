<?php

namespace App\Repository;

use App\Entity\AssetTypes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method AssetTypes|null find($id, $lockMode = null, $lockVersion = null)
 * @method AssetTypes|null findOneBy(array $criteria, array $orderBy = null)
 * @method AssetTypes[]    findAll()
 * @method AssetTypes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssetTypesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AssetTypes::class);
    }

    // /**
    //  * @return AssetTypes[] Returns an array of AssetTypes objects
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
    public function findOneBySomeField($value): ?AssetTypes
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
