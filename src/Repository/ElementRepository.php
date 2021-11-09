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
            ->orderBy('e.name', 'ASC')
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getElementByScientificName($name)
    {
        return $this->createQueryBuilder('e')
            ->where('e.scientificName = :name')
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
            ->orderBy('e.name', 'ASC')
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
            ->orderBy('e.name', 'ASC')
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Search a value in the living thing
     * 
     * @param string the value to search
     * @param int offset
     * @param int limit
     * @return Element[]|[] an array of elements or an empty array
     */
    public function searchElements(string $searchedValue, int $offset, int $limit)
    {
        return $this->createQueryBuilder('e')
            ->where('e.name LIKE :searchedValue OR e.scientificName LIKE :searchedValue')
            ->orderBy('e.name', 'ASC')
            ->setParameter('searchedValue', "%{$searchedValue}%")
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

    public function countElementsWithArticle()
    {
        return $this->createQueryBuilder('e')
            ->select('count(e.id) as nbrElements')
            ->leftJoin('e.articleElement', 'aE')
            ->where('aE.id IS NOT NULL')
            ->getQuery()
            ->getSingleResult()["nbrElements"]
        ;
    }

    public function countElementsWithoutArticle()
    {
        return $this->createQueryBuilder('e')
            ->select('count(e.id) as nbrElements')
            ->leftJoin('e.articleElement', 'aE')
            ->where('aE.id IS NULL')
            ->getQuery()
            ->getSingleResult()["nbrElements"]
        ;
    }

    /**
     * Count elements corresponding to the searched value
     * 
     * @param string the searched value
     * @return int the number of element corresponding of the searched value
     */
    public function countSearchElements(string $searchedValue)
    {
        return $this->createQueryBuilder('e')
            ->select('count(e.id) as nbrElements')
            ->where('e.name LIKE :searchedValue OR e.scientificName LIKE :searchedValue')
            ->setParameter('searchedValue', "%{$searchedValue}%")
            ->getQuery()
            ->getSingleResult()["nbrElements"]
        ;
    }
}
