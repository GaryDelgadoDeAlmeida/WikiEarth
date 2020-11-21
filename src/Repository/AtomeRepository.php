<?php

namespace App\Repository;

use App\Entity\Atome;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Atome|null find($id, $lockMode = null, $lockVersion = null)
 * @method Atome|null findOneBy(array $criteria, array $orderBy = null)
 * @method Atome[]    findAll()
 * @method Atome[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AtomeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Atome::class);
    }

    public function countAtomes()
    {
        return $this->createQueryBuilder('a')
            ->select('count(a.id) as nbrAtomes')
            ->getQuery()
            ->getSingleResult()["nbrAtomes"];
        ;
    }

    // /**
    //  * @return Atome[] Returns an array of Atome objects
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
    public function findOneBySomeField($value): ?Atome
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
