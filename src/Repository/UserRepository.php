<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Get a single user
     * 
     * @param int user id
     * @return User|null Returns a object User or an empty array
     */
    public function getUser(int $user_id)
    {
        return $this->createQueryBuilder("u")
            ->where("u.id = :user_id")
            ->setParameter("user_id", $user_id)
            ->getQuery()
            ->getOneOrNullResult();
        ;
    }

    public function getUsers(int $offset, int $limit, int $current_admin_id)
    {
        return $this->createQueryBuilder('u')
            ->where('u.id != :current_admin_id')
            ->orderBy('u.lastname', 'ASC', 'u.firstname', 'ASC')
            ->setParameter('current_admin_id', $current_admin_id)
            ->setFirstResult($offset * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getUserByLogin(string $login)
    {
        return $this->createQueryBuilder('u')
            ->where('u.login LIKE :login')
            ->setParameter('login', $login)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function countUsers(int $current_admin_id)
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id) as nbrUsers')
            ->where('u.id != :current_admin_id')
            ->setParameter('current_admin_id', $current_admin_id)
            ->getQuery()
            ->getSingleResult()["nbrUsers"];
    }
}
