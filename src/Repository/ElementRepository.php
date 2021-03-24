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
        return $this->createQueryBuilder('a')
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
        ;
    }

    public function getScientificName($name)
    {
        return $this->createQueryBuilder('a')
        ->where('a.scientific_name = :name')
        ->setParameter("name", $name)
        ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function countElements()
    {
        return $this->createQueryBuilder('a')
            ->select('count(a.id) as nbrElements')
            ->getQuery()
            ->getSingleResult()["nbrElements"];
        ;
    }
}
