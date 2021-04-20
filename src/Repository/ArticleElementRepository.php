<?php

namespace App\Repository;

use App\Entity\ArticleElement;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ArticleElement|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticleElement|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticleElement[]    findAll()
 * @method ArticleElement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleElementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticleElement::class);
    }

    public function getArticleElements($offset, $limit)
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('App\Entity\Element', 'e', Join::WITH, 'e.id = a.element')
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getArticleElementsApprouved($offset, $limit)
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('App\Entity\Element', 'e', Join::WITH, 'e.id = a.element')
            ->where('a.approved = 1')
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function countArticleElements()
    {
        return $this->createQueryBuilder('a')
            ->select('count(a.id) as nbrElements')
            ->where('a.approved = 1')
            ->getQuery()
            ->getSingleResult()["nbrElements"]
        ;
    }

    public function countArticleElementsApprouved()
    {
        return $this->createQueryBuilder('a')
            ->select('count(a.id) as nbrElements')
            ->leftJoin("a.element", "e")
            ->where('a.approved = 1')
            ->andWhere("e.name IS NOT NULL")
            ->getQuery()
            ->getSingleResult()["nbrElements"]
        ;
    }
}
