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
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(ArticleLivingThing $entity, bool $flush = true) : void {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(ArticleLivingThing $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
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
     * @param offset Parameter offset is the page
     * @param limit Parameter limit is the number of element per page
     * @return ArticleLivingThings[]
     */
    public function getArticleLivingThingsApproved($offset, $limit)
    {
        return $this->createQueryBuilder('a')
            ->where('a.approved = 1')
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param offset Parameter offset is the page
     * @param limit Parameter limit is the number of element per page
     * @return ArticleLivingThings[]
     */
    public function getArticleLivingThingsNotApproved($offset, $limit)
    {
        return $this->createQueryBuilder('a')
            ->where('a.approved = 0')
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param offset Parameter offset is the page
     * @param limit Parameter limit is the number of element per page
     * @return ArticleLivingThings[]
     */
    public function getArticleLivingThingsDesc($offset, $limit)
    {
        return $this->createQueryBuilder('a')
            ->where('a.approved = 1')
            ->orderBy('a.createdAt', "DESC")
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param id
     * @return ArticleLivingThings
     */
    public function getArticleLivingThingApproved($id)
    {
        return $this->createQueryBuilder('a')
            ->where('a.id = :id')
            ->andWhere('a.approved = 1')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param id
     * @return ArticleLivingThings
     */
    public function getArticleLivingThingNotApproved($id)
    {
        return $this->createQueryBuilder('a')
            ->where('a.id = :id')
            ->andWhere('a.approved = 0')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param kingdom
     * @return ArticleLivingThings[]
     */
    public function getArticleLivingThingsByLivingThingKingdom($kingdom, $offset, $limit)
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('App\Entity\LivingThing', 'l', Join::WITH, 'l.id = a.idLivingThing')
            ->where('l.kingdom = :kingdom')
            ->andWhere('a.approved = 1')
            ->setParameter('kingdom', $kingdom)
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
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
            ->andWhere('a.approved = 1')
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
            ->where('a.approved = 0')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return ArticleLivingThings[]
     */
    public function searchArticleLivingThings($searchedValue)
    {
        return $this->createQueryBuilder('a')
            ->where('a.title LIKE :searchValue')
            ->orWhere('a.geography LIKE :searchValue')
            ->orWhere('a.ecology LIKE :searchValue')
            ->orWhere('a.behaviour LIKE :searchValue')
            ->orWhere('a.wayOfLife LIKE :searchValue')
            ->orWhere('a.description LIKE :searchValue')
            ->orWhere('a.otherData LIKE :searchValue')
            ->andWhere('a.approved = 1')
            ->setParameter('searchValue', '%' . $searchedValue . '%')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return ArticleLivingThings
     */
    public function countArticleLivingThings()
    {
        return $this->createQueryBuilder('a')
            ->select('count(a.id) as nbrArticles')
            ->getQuery()
            ->getSingleResult()["nbrArticles"];
    }

    /**
     * @return ArticleLivingThings
     */
    public function countArticleLivingThingsApproved()
    {
        return $this->createQueryBuilder('a')
            ->select('count(a.id) as nbrArticles')
            ->where('a.approved = 1')
            ->getQuery()
            ->getSingleResult()["nbrArticles"];
    }

    /**
     * @return ArticleLivingThings
     */
    public function countArticleLivingThingsNotApproved()
    {
        return $this->createQueryBuilder('a')
            ->select('count(a.id) as nbrArticles')
            ->where('a.approved = 0')
            ->getQuery()
            ->getSingleResult()["nbrArticles"];
    }

    /**
     * @param limit Parameter limit is the number of element per page
     * @return ArticleLivingThings
     */
    public function countArticleLivingThingsByKingdom($kingdom, $limit)
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a.id) / :limit as nbrOffset')
            ->innerJoin('App\Entity\LivingThing', 'l', Join::WITH, 'l.id = a.idLivingThing')
            ->where('l.kingdom = :kingdom')
            ->andWhere('a.approved = 1')
            ->setParameter('limit', $limit)
            ->setParameter('kingdom', $kingdom)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function countArticleLivingThingsUser($user_id)
    {
        return $this->createQueryBuilder('a')
            ->select('count(a.id) as nbrUserArticle')
            ->where("a.user = :user_id")
            ->setParameter('user_id', $user_id)
            ->getQuery()
            ->getSingleResult()["nbrUserArticle"];
    }

    public function countSearchArticleLivingThings($searchedValue)
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a.id) as nbrUserArticle')
            // ->innerJoin('App\Entity\LivingThing', 'l', Join::WITH, 'l.id = a.idLivingThing')
            ->where('a.title LIKE :searchValue')
            ->orWhere('a.geography LIKE :searchValue')
            ->orWhere('a.ecology LIKE :searchValue')
            ->orWhere('a.behaviour LIKE :searchValue')
            ->orWhere('a.wayOfLife LIKE :searchValue')
            ->orWhere('a.description LIKE :searchValue')
            ->orWhere('a.otherData LIKE :searchValue')
            ->andWhere('a.approved = 1')
            ->setParameter('searchValue', '%' . $searchedValue . '%')
            ->getQuery()
            ->getSingleResult()["nbrUserArticle"];
    }
}
