<?php

namespace App\Repository;

use Doctrine\ORM\Query\Expr\Join;
use App\Entity\ArticleLivingThing;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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

    /**
     * @param offset Parameter offset is the page
     * @param limit Parameter limit is the number of element per page
     * @return ArticleLivingThings[]
     */
    public function getArticleLivingThings($offset, $limit)
    {
        return $this->createQueryBuilder('a')
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param idLivingThing
     * @return ArticleLivingThings
     */
    public function getArticleLivingThingByLivingThingId($idLivingThing)
    {
        return $this->createQueryBuilder('a')
            ->where('a.idLivingThing = :idLivingThing')
            ->setParameter('idLivingThing', $idLivingThing)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param kingdom
     * @return ArticleLivingThings[]
     */
    public function getArticleLivingThingsByLivingThingKingdom($kingdom)
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('App\Entity\LivingThing', 'l', Join::WITH, 'l.id = a.idLivingThing')
            ->where('l.kingdom = :kingdom')
            ->setParameter('kingdom', $kingdom)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param kingdom
     * @param id
     * @return ArticleLivingThings
     */
    public function getArticleLivingThingsByLivingThingKingdomByID($kingdom, $id)
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('App\Entity\LivingThing', 'l', Join::WITH, 'l.id = a.idLivingThing')
            ->where('l.kingdom = :kingdom')
            ->andWhere('l.id = :id')
            ->setParameter('kingdom', $kingdom)
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return ArticleLivingThings[]
     */
    public function getArticleLivingThingsByArticleNotTreatedToPublish()
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('App\Entity\LivingThing', 'l', Join::WITH, 'l.id = a.idLivingThing')
            ->where('l.isTreated = 0')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param id
     * @return ArticleLivingThings
     */
    public function getArticleLivingThingsByArticleNotTreatedToPublishById($id)
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('App\Entity\LivingThing', 'l', Join::WITH, 'l.id = a.idLivingThing')
            ->where('l.isTreated = 0')
            ->andWhere('l.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * @return ArticleLivingThings
     */
    public function countArticleLivingThings()
    {
        return $this->createQueryBuilder('a')
            ->select('count(a.id)')
            ->getQuery()
            ->getSingleResult();
    }
}
