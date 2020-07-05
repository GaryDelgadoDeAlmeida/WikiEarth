<?php

namespace App\Repository;

use App\Entity\ArticleLivingThing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ArticleLivingThing|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticleLivingThing|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticleLivingThing[]    findAll()
 * @method ArticleLivingThing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleLivingThingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticleLivingThing::class);
    }

    public function getArticleLivingThings($offset, $limit)
    {
        return $this->createQueryBuilder('a')
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function countArticleLivingThings()
    {
        return $this->createQueryBuilder('a')
            ->select('count(a.id)')
            ->getQuery()
            ->getSingleResult();
    }

    // /**
    //  * @return ArticleLivingThing[] Returns an array of ArticleLivingThing objects
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
    public function findOneBySomeField($value): ?ArticleLivingThing
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
