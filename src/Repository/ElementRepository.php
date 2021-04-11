<?php

namespace App\Repository;

use App\Entity\Element;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Element|null find($id, $lockMode = null, $lockVersion = null)
 * @method Element|null findOneBy(array $criteria, array $orderBy = null)
 * @method Element[]    findAll()
 * @method Element[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ElementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Element::class);
    }

    public function getElements($offset, $limit)
    {
        return $this->createQueryBuilder('e')
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getElementByScientificName($name)
    {
        return $this->createQueryBuilder('e')
            ->where('e.scientific_name = :name')
            ->setParameter("name", $name)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getElementsWithArticle($offset, $limit)
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.articleElement', 'aE')
            ->where('aE.id IS NOT NULL')
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getElementsWithoutArticle($offset, $limit)
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.articleElement', 'aE')
            ->where('aE.id IS NULL')
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function countElements()
    {
        return $this->createQueryBuilder('e')
            ->select('count(e.id) as nbrElements')
            ->getQuery()
            ->getSingleResult()["nbrElements"]
        ;
    }

    public function countElementsWithArticle($offset, $limit)
    {
        return $this->createQueryBuilder('e')
            ->select('count(e.id) as nbrElements')
            ->leftJoin('e.articleElement', 'aE')
            ->where('aE.id IS NOT NULL')
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getSingleResult()["nbrElements"]
        ;
    }

    public function countElementsWithoutArticle($offset, $limit)
    {
        return $this->createQueryBuilder('e')
            ->select('count(e.id) as nbrElements')
            ->leftJoin('e.articleElement', 'aE')
            ->where('aE.id IS NULL')
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getSingleResult()["nbrElements"]
        ;
    }
}
