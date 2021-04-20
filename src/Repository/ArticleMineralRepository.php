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

    public function getArticleMineral(int $articleId)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.mineral', 'm')
            ->where('a.id = :articleId')
            ->setParameter('articleId', $articleId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function countArticleMinerals()
    {
        return $this->createQueryBuilder('a')
            ->select('count(a.id) as nbrMinerals')
            ->where('a.approved = 1')
            ->getQuery()
            ->getSingleResult()["nbrMinerals"]
        ;
    }

    public function countArticleMineralsApprouved()
    {
        return $this->createQueryBuilder('a')
            ->select('count(a.id) as nbrMinerals')
            ->leftJoin('a.mineral', 'm')
            ->where('a.approved = 1')
            ->andWhere('m.name IS NOT NULL')
            ->getQuery()
            ->getSingleResult()["nbrMinerals"]
        ;
    }
}
