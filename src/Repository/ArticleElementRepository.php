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
            ->getSingleResult()["nbrElements"];
        ;
    }

    // /**
    //  * @return ArticleElement[] Returns an array of ArticleElement objects
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
    public function findOneBySomeField($value): ?ArticleElement
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
