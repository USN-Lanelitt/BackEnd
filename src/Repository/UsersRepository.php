<?php

namespace App\Repository;

use App\Entity\Users;
use App\Entity\Chat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Users|null find($id, $lockMode = null, $lockVersion = null)
 * @method Users|null findOneBy(array $criteria, array $orderBy = null)
 * @method Users[]    findAll()
 * @method Users[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Users::class);
    }

    // /**
    //  * @return Users[] Returns an array of Users objects
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
    public function findOneBySomeField($value): ?Users
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /*Finn har gjort findEmail*/
    public function findEmail($sEmail)
    {
        return $this->createQueryBuilder('u')
                    ->andWhere('u.email = :email')
                    ->setParameter('email', $sEmail)
                    ->getQuery()
                    ->getResult();
    }
    /*Finn har gjort updateProfileImage*/
    public function updateProfileImage($iUserId, $sNewfilename)
    {
        return $this->createQueryBuilder('u')
                    ->update()
                    ->set('u.profile_image', 'profile_image')
                    ->setParameter('profile_image', $sNewfilename)
                    ->where('u.id', 'userid')
                    ->setParameter('userid', $iUserId)
                    ->getQuery()
                    ->execute();
    }
    /*public function  findChatUser($userId){
        $subQuery= $this->_em->createQueryBuilder()
                        ->select('DISTINCT c.user2_id')
                        ->from('chat', 'c')
                        ->where('c.user1_id=userId')
                        ->setParameter('userId', $userId)
                        ->getQuery()
                        ->getArrayResult();
        $query=$this->createQueryBuilder('u');

        $query ->andWhere($query->expr()->in('u.id',$subQuery))
                ->getQuery()
                ->getArrayResult();

        return $query;
    }*/
}
