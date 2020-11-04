<?php

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    /**
     * Get les notifications d'un user dans l'ordre de création croissante
     */
    public function getNotifications($user, $offset, $limit)
    {
        return $this->createQueryBuilder('n')
            ->where("n.user = :user")
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
        ;
    }


    /**
     * Get les notifications d'un user dans l'ordre de création décroissante
     */
    public function getLatestNotifications($user, $offset, $limit)
    {
        return $this->createQueryBuilder('n')
            ->where("n.user = :user")
            ->orderBy('n.createdAt', 'DESC')
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
        ;
    }

    public function countNotification($user)
    {
        return $this->createQueryBuilder('n')
            ->select('count(n.id) as nbrNotif')
            ->where('n.user = :user')
            ->orderBy('n.createdAt', 'DESC')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult()["nbrNotif"];
        ;
    }

    // /**
    //  * @return Notification[] Returns an array of Notification objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Notification
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
