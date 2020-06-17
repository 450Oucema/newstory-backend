<?php

namespace App\Repository;

use App\Entity\FriendRequest;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FriendRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method FriendRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method FriendRequest[]    findAll()
 * @method FriendRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FriendRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FriendRequest::class);
    }

    // /**
    //  * @return FriendRequest[] Returns an array of FriendRequest objects
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
    public function findOneBySomeField($value): ?FriendRequest
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findAlerts(User $user)
    {
        return $this->createQueryBuilder('f')
            ->join('f.user_asking', 'uask')
            ->join('f.user_responding', 'urespond')
            ->select('uask.email, uask.id, f.created_at, f.accepted, f.responseDate, f.uuid')
            ->andWhere('urespond = :user')
            ->andWhere('f.accepted is NULL')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}
