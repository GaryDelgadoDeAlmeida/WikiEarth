<?php

namespace App\Repository;

use App\Entity\LivingThing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LivingThing|null find($id, $lockMode = null, $lockVersion = null)
 * @method LivingThing|null findOneBy(array $criteria, array $orderBy = null)
 * @method LivingThing[]    findAll()
 * @method LivingThing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivingThingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LivingThing::class);
    }

    public function getLivingThings($offset, $limit)
    {
        return $this->createQueryBuilder('a')
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return LivingThing[] Returns an array of LivingThing objects
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
    public function findOneBySomeField($value): ?LivingThing
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
