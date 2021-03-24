<?php

namespace App\Repository;

use App\Entity\LivingThing;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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
        return $this->createQueryBuilder('l')
            ->orderBy('l.name', 'ASC', 'l.commonName', 'ASC')
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getLivingThing($id)
    {
        return $this->createQueryBuilder('l')
            ->where('l.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getLivingThingByName($name)
    {
        return $this->createQueryBuilder('l')
            ->where('l.name = :name OR l.commonName = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getLivingThingArticle($offset, $limit)
    {
        return $this->createQueryBuilder('l')
            ->innerJoin('App\Entity\ArticleLivingThing', 'a', Join::WITH, 'a.idLivingThing = l.id')
            ->where("a.idLivingThing != :status")
            ->setParameter(":status", '')
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function searchLivingThing($search, $offset, $limit)
    {
        return $this->createQueryBuilder('l')
            ->where('l.commonName LIKE :search OR l.name LIKE :search OR l.genus LIKE :search OR l.subGenus LIKE :search OR l.species LIKE :search OR l.subSpecies LIKE :search')
            ->orderBy('l.name', 'ASC', 'l.commonName', 'ASC')
            ->setParameter('search', "%" . $search . "%")
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function countLivingThings()
    {
        return $this->createQueryBuilder('l')
            ->select('count(l.id) as nbrLivingThing')
            ->getQuery()
            ->getSingleResult()["nbrLivingThing"];
    }

    public function countLivingThingKingdom($kingdom)
    {
        return $this->createQueryBuilder('l')
            ->select('count(l.id) as nbrLivingThing')
            ->where('l.kingdom = :kingdom')
            ->setParameter('kingdom', $kingdom)
            ->getQuery()
            ->getSingleResult()["nbrLivingThing"];
    }

    public function countSearchLivingThing($search)
    {
        return $this->createQueryBuilder('l')
            ->select('count(l.id) as nbrSearchLivingThing')
            ->where('l.commonName LIKE :search OR l.name LIKE :search OR l.genus LIKE :search OR l.subGenus LIKE :search OR l.species LIKE :search OR l.subSpecies LIKE :search')
            ->setParameter('search', "%" . $search . "%")
            ->getQuery()
            ->getSingleResult()["nbrSearchLivingThing"];
    }
}
