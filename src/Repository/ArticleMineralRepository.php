<?php

namespace App\Repository;

use App\Entity\ArticleMineral;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ArticleMineral|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticleMineral|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticleMineral[]    findAll()
 * @method ArticleMineral[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleMineralRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticleMineral::class);
    }

    public function getArticleMinerals($offset, $limit)
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('App\Entity\Mineral', 'e', Join::WITH, 'e.id = a.mineral')
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getArticleMineralsApprouved($offset, $limit)
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('App\Entity\Mineral', 'e', Join::WITH, 'e.id = a.mineral')
            ->where('a.approved = 1')
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function countArticleMinerals()
    {
        return $this->createQueryBuilder('a')
            ->select('count(a.id) as nbrMinerals')
            ->where('a.approved = 1')
            ->getQuery()
            ->getSingleResult()["nbrMinerals"];
        ;
    }

    // /**
    //  * @return ArticleMineral[] Returns an array of ArticleMineral objects
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
    public function findOneBySomeField($value): ?ArticleMineral
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
