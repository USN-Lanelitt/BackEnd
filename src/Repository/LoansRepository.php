<?php

namespace App\Repository;

use App\Entity\Loans;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Loans|null find($id, $lockMode = null, $lockVersion = null)
 * @method Loans|null findOneBy(array $criteria, array $orderBy = null)
 * @method Loans[]    findAll()
 * @method Loans[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LoansRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Loans::class);
    }

    // /**
    //  * @return Loans[] Returns an array of Loans objects
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
    public function findOneBySomeField($value): ?Loans
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


    public function findAllAssetLoans($assetId)
    {
        $sStatus = "accepted";
        return $this->createQueryBuilder('l')
            ->andWhere('l.assets = :id')
            ->andWhere('l.statusLoan = :status')
            ->setParameter('id', $assetId)
            ->setParameter('status', $sStatus)
            ->orderBy('l.id', 'ASC')
            ->getQuery()
            ->getArrayResult()
            ;
    }

    public function findAllStatusSent($userId)
    {
        $sStatusSent = 0;
        return $this->createQueryBuilder('l')
            ->andWhere('l.users = :id')
            ->andWhere('l.statusLoan = :status')
            ->setParameter('id', $userId)
            ->setParameter('status', $sStatusSent)
            ->orderBy('l.id', 'ASC')
            ->getQuery()
            ->getArrayResult()
            ;
    }

    public function findAllStatusDenied($userId)
    {
        $sStatusSent = 2;
        return $this->createQueryBuilder('l')
            ->andWhere('l.users = :id')
            ->andWhere('l.statusLoan = :status')
            ->setParameter('id', $userId)
            ->setParameter('status', $sStatusSent)
            ->orderBy('l.id', 'ASC')
            ->getQuery()
            ->getArrayResult()
            ;
    }
}
