<?php

namespace App\Repository;

use App\Entity\UserConnections;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method UserConnections|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserConnections|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserConnections[]    findAll()
 * @method UserConnections[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserConnectionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserConnections::class);
    }

    // /**
    //  * @return UserConnections[] Returns an array of UserConnections objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserConnections
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }*/

    public function findFriend($user, $friend)
    {
        //1 = vennskap
        $status = 1;
        return $this->createQueryBuilder('u')
            ->andWhere('u.user1 = :user')
            ->andWhere('u.user2 = :friend')
            ->andWhere('u.requestStatus = :status')
            ->setParameter('user', $user)
            ->setParameter('friend', $friend)
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findFriends($user)
    {
        //1 = vennskap
        $status = 1;
        return $this->createQueryBuilder('u')
            ->andWhere('u.user1 = :user')
            ->andWhere('u.requestStatus = :status')
            ->setParameter('user', $user)
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult()
            ;
    }
    
    public function findFriendRequest($user)
    {
        //0 = vennskap
        $status = 0;
        return $this->createQueryBuilder('u')
            ->andWhere('u.user2 = :user')
            ->andWhere('u.requestStatus = :status')
            ->setParameter('user', $user)
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult()
            ;
    }
}
