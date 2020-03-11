<?php

namespace App\Repository;

use App\Entity\IllegalWords;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method IllegalWords|null find($id, $lockMode = null, $lockVersion = null)
 * @method IllegalWords|null findOneBy(array $criteria, array $orderBy = null)
 * @method IllegalWords[]    findAll()
 * @method IllegalWords[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IllegalWordsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IllegalWords::class);
    }

    // /**
    //  * @return IllegalWords[] Returns an array of IllegalWords objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?IllegalWords
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
