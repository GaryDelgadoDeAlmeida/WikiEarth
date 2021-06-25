<?php

namespace App\Repository;

use App\Entity\Mineral;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Mineral|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mineral|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mineral[]    findAll()
 * @method Mineral[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MineralRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mineral::class);
    }

    /**
     * @param int offset
     * @param int limit
     * @return Mineral[]|[]
     */
    public function getMinerals(int $offset, int $limit)
    {
        return $this->createQueryBuilder('m')
            ->orderBy('m.name', 'ASC')
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getMineralByName(string $mineral_name)
    {
        return $this->createQueryBuilder('m')
            ->where('m.name = :mineral_name')
            ->setParameter('mineral_name', $mineral_name)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getMineralsWithArticle(int $offset, int $limit)
    {
        return $this->createQueryBuilder('m')
            ->leftJoin("m.articleMineral", "aM")
            ->where('aM.id IS NOT NULL')
            ->orderBy('m.name', 'ASC')
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getMineralsWithoutArticle(int $offset, int $limit)
    {
        return $this->createQueryBuilder('m')
            ->leftJoin("m.articleMineral", "aM")
            ->where('aM.id IS NULL')
            ->orderBy('m.name', 'ASC')
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function searchMineral(string $searchedValue, int $offset, int $limit)
    {
        return $this->createQueryBuilder('m')
            ->where('m.name LIKE :searchedValue')
            ->orderBy('m.name', 'ASC')
            ->setParameter('searchedValue', "%{$searchedValue}%")
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function countMinerals()
    {
        return $this->createQueryBuilder('m')
            ->select('count(m.id) as nbrMinerals')
            ->getQuery()
            ->getSingleResult()['nbrMinerals']
        ;
    }

    public function countMineralsWithArticle()
    {
        return $this->createQueryBuilder('m')
            ->select('count(m.id) as nbrMinerals')
            ->leftJoin("m.articleMineral", "aM")
            ->where('aM.id IS NOT NULL')
            ->getQuery()
            ->getSingleResult()['nbrMinerals']
        ;
    }

    /**
     * @return int number of mineral who don't have any articke
     */
    public function countMineralsWithoutArticle()
    {
        return $this->createQueryBuilder('m')
            ->select('count(m.id) as nbrMinerals')
            ->leftJoin("m.articleMineral", "aM")
            ->where('aM.id IS NULL')
            ->getQuery()
            ->getSingleResult()['nbrMinerals']
        ;
    }

    /**
     * @param string the searched value
     * @return int number of mineral corresponding to the searched value
     */
    public function countSearchMineral(string $searchedValue)
    {
        return $this->createQueryBuilder('m')
            ->select('count(m.id) as nbrMinerals')
            ->where('m.name LIKE :searchedValue')
            ->setParameter('searchedValue', "%{$searchedValue}%")
            ->getQuery()
            ->getSingleResult()['nbrMinerals']
        ;
    }
}
