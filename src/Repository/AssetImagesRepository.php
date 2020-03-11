<?php

namespace App\Repository;

use App\Entity\AssetImages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method AssetImages|null find($id, $lockMode = null, $lockVersion = null)
 * @method AssetImages|null findOneBy(array $criteria, array $orderBy = null)
 * @method AssetImages[]    findAll()
 * @method AssetImages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssetImagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AssetImages::class);
    }

    // /**
    //  * @return AssetImages[] Returns an array of AssetImages objects
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
    public function findOneBySomeField($value): ?AssetImages
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
