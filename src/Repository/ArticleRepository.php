<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * Get articles independently if mineral or living thing or something other approved by the staff.
     * 
     * @param int offset
     * @param int limit of elements per page
     * @return Article[]|[] array of approved article
     */
    public function getArticlesApproved(int $offset, int $limit)
    {
        return $this->createQueryBuilder('a')
            ->where('a.approved = 1')
            ->orderBy("a.createdAt", "DESC")
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Search articles with a given value.
     * 
     * @param string the value to search in the article
     * @param int offset
     * @param int limit of elements per page
     * @return Article[]|[] list of article who match the criteria
     */
    public function searchArticles(string $searchedValue, int $offset, int $limit)
    {
        $query = $this->createQueryBuilder('a');
        
        return $query
            ->leftJoin("a.articleLivingThing", "aLT")
            ->leftJoin("a.articleElement", "aE")
            ->leftJoin("a.articleMineral", "aM")
            ->where($query->expr()->orx(
                $query->expr()->like("a.title", ":searchedValue"),
                
                // Article Living Thing
                $query->expr()->andX(
                    $query->expr()->isNotNull('aLT.id'),
                    $query->expr()->orx(
                        $query->expr()->like("aLT.geography", ":searchedValue"),
                        $query->expr()->like("aLT.ecology", ":searchedValue"),
                        $query->expr()->like("aLT.behaviour", ":searchedValue"),
                        $query->expr()->like("aLT.wayOfLife", ":searchedValue"),
                        $query->expr()->like("aLT.description", ":searchedValue"),
                        $query->expr()->like("aLT.otherData", ":searchedValue")
                    )
                ),
                
                // Article Element
                $query->expr()->andX(
                    $query->expr()->isNotNull('aE.id'),
                    $query->expr()->orx(
                        $query->expr()->like("aE.generality", ":searchedValue"),
                        $query->expr()->like("aE.description", ":searchedValue"),
                        $query->expr()->like("aE.characteristics", ":searchedValue"),
                        $query->expr()->like("aE.property", ":searchedValue"),
                        $query->expr()->like("aE.utilization", ":searchedValue")
                    )
                ),
                
                // Article Mineral
                $query->expr()->andX(
                    $query->expr()->isNotNull('aM.id'),
                    $query->expr()->orx(
                        $query->expr()->like("aM.generality", ":searchedValue"),
                        $query->expr()->like("aM.etymology", ":searchedValue"),
                        $query->expr()->like("aM.properties", ":searchedValue"),
                        $query->expr()->like("aM.geology", ":searchedValue"),
                        $query->expr()->like("aM.mining", ":searchedValue")
                    )
                )
            ))
            ->andWhere('a.approved = 1')
            ->orderBy("a.title", "ASC")
            ->setParameter("searchedValue", "%{$searchedValue}%")
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Get article living thing checking if article is linked to the living thing
     * 
     * @param int article id
     * @param Article|[]
     */
    public function getArticleLivingThing(int $articleId)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin("a.articleLivingThing", "aLT")
            ->leftJoin("aLT.livingThing", "l")
            ->where("a.id = :id")
            ->andWhere("l.id IS NOT NULL")
            ->setParameter("id", $articleId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Get article by living thing database id. It's not importante if approved
     * true or false
     * 
     * @param int id du living thing
     * @return Article|[] retourne l'article obtenu ou un tableau vide
     */
    public function getArticleByLivingThing(int $livingThingId)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin("a.articleLivingThing", "aLT")
            ->leftJoin("aLT.livingThing", "l")
            ->where("l.id = :id")
            ->setParameter("id", $livingThingId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Get articles of living things approved or not into the database
     * 
     * @param int offset
     * @param int number of elements per page
     * @return Article[]|[]
     */
    public function getArticleLivingThings(int $offset, int $limit)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin("a.articleLivingThing", "aLT")
            ->leftJoin("aLT.livingThing", "l")
            ->where("a.articleMineral IS NULL")
            ->andWhere("a.articleElement IS NULL")
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get a single article of living thing from a specific kingdom and the id in the database
     * 
     * @param string kingdom
     * @param int id
     * @return Article|null
     */
    public function getArticleLivingThingsByLivingThingKingdomByID(string $kingdom, int $id)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin("a.articleLivingThing", "aLT")
            ->leftJoin("aLT.livingThing", "l")
            ->where('l.kingdom = :kingdom')
            ->andWhere('a.approved = 1')
            ->andWhere('l.id = :id')
            ->setParameter('kingdom', $kingdom)
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Get articles of living things approved from a specific kingdom (Bacteria, Archaea, Protozoa, Chromista, Plantae, Fungi or Animalia)
     * 
     * @param string kingdom of the living thing
     * @param int offset (page)
     * @param int limit per page items
     * @return array Article[]|[]
     */
    public function getArticleLivingThingsByLivingThingKingdom(string $kingdom, int $offset, int $limit)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin("a.articleLivingThing", "aLT")
            ->leftJoin("aLT.livingThing", "l")
            ->where('l.kingdom = :kingdom')
            ->andWhere('a.approved = 1')
            ->andWhere("l.id IS NOT NULL")
            ->setParameter('kingdom', $kingdom)
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param int offset Parameter offset is the page
     * @param int limit Parameter limit is the number of element per page
     * @return ArticleLivingThings[]|[]
     */
    public function getArticleLivingThingsApproved(int $offset, int $limit)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin("a.articleLivingThing", "aLT")
            ->leftJoin("aLT.livingThing", "l")
            ->where('a.approved = 1')
            ->andWhere("l.id IS NOT NULL")
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int offset Parameter offset is the page
     * @param int limit Parameter limit is the number of element per page
     * @return Article[]|[]
     */
    public function getArticleLivingThingsDesc(int $offset, int $limit)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin("a.articleLivingThing", "aLT")
            ->leftJoin("aLT.livingThing", "l")
            ->where('a.approved = 1')
            ->andWhere("l.id IS NOT NULL")
            ->orderBy('a.createdAt', "DESC")
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Get article element checking if element id isn't null
     * 
     * @param int article id
     * @return Article|[]
     */
    public function getArticleElement(int $articleId)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin("a.articleElement", "aE")
            ->leftJoin("aE.element", "e")
            ->where("a.id = :id")
            ->andWhere("e.id IS NOT NULL")
            ->setParameter("id", $articleId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Get article by element database id. It's not importante if approved
     * true or false
     * 
     * @param int id du element
     * @return Article|[] retourne l'article obtenu ou un tableau vide
     */
    public function getArticleByElement(int $elementId)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin("a.articleElement", "aE")
            ->leftJoin("aE.element", "e")
            ->where("e.id = :id")
            ->setParameter("id", $elementId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Get articles of element approved or not into the database.
     * 
     * @param int offset (page)
     * @param int limit per page items
     * @return Article[]|[]
     */
    public function getArticleElements(int $offset, int $limit)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin("a.articleElement", "aE")
            ->leftJoin("aE.element", "e")
            ->andWhere("e.id IS NOT NULL")
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Get articles of element approved (by the staff) of the database.
     * 
     * @param int offset (page)
     * @param int limit per page items
     * @return Article[]|[]
     */
    public function getArticleElementsApproved(int $offset, int $limit)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin("a.articleElement", "aE")
            ->leftJoin("aE.element", "e")
            ->where('a.approved = 1')
            ->andWhere("e.id IS NOT NULL")
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Get article mineral checking article is linked to mineral
     * 
     * @param int mineral id
     * @return Article|[] retourne l'article si trouvé sinon un tableau vide
     */
    public function getArticleMineral(int $articleId)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin("a.articleMineral", "aLT")
            ->leftJoin("aLT.mineral", "m")
            ->where("a.id = :id")
            ->andWhere("m.id IS NOT NULL")
            ->setParameter("id", $articleId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Get article by mineral database id. We don't care if approved
     * true or false
     * 
     * @param int mineral id
     * @return Article|[] retourne l'article si trouvé sinon un tableau vide
     */
    public function getArticleByMineral(int $mineralId)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin("a.articleMineral", "aLT")
            ->leftJoin("aLT.mineral", "m")
            ->where("m.id = :id")
            ->setParameter("id", $mineralId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Get articles of mineral approved or not into the database.
     * 
     * @param int offset
     * @param int limit
     * @return Article[]|[]
     */
    public function getArticleMinerals(int $offset, int $limit)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin("a.articleMineral", "aE")
            ->leftJoin("aE.mineral", "m")
            ->andWhere("m.id IS NOT NULL")
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Get articles of mineral approved (by the staff) of the database.
     * 
     * @param int offset
     * @param int limit
     * @return Article[]|[]
     */
    public function getArticleMineralsApproved(int $offset, int $limit)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin("a.articleMineral", "aE")
            ->leftJoin("aE.mineral", "m")
            ->where('a.approved = 1')
            ->andWhere("m.id IS NOT NULL")
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Search a determinated value in article of living thing type
     * 
     * @param string the searched value in an article
     * @return ArticleLivingThings[]|[]
     */
    public function searchArticleLivingThings(string $searchedValue)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin("a.articleLivingThing", "aL")
            ->leftJoin("aL.livingThing", "l")
            ->where('a.title LIKE :searchValue')
            ->orWhere('aL.geography LIKE :searchValue')
            ->orWhere('aL.ecology LIKE :searchValue')
            ->orWhere('aL.behaviour LIKE :searchValue')
            ->orWhere('aL.wayOfLife LIKE :searchValue')
            ->orWhere('aL.description LIKE :searchValue')
            ->orWhere('aL.otherData LIKE :searchValue')
            ->andWhere('a.approved = 1')
            ->andWhere('l.id IS NOT NULL')
            ->setParameter('searchValue', '%' . $searchedValue . '%')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Count all articles (living thing, element, mineral) approved (by the staff) of the database.
     * 
     * @return int number of all articles (living thing, element, mineral) approved
     */
    public function countArticlesApproved()
    {
        return $this->createQueryBuilder('a')
            ->select('count(a.id) as nbrArticles')
            ->where('a.approved = 1')
            ->getQuery()
            ->getSingleResult()["nbrArticles"]
        ;
    }

    /**
     * Count articles of living thing type approved or not into the database
     * 
     * @return ArticleLivingThings
     */
    public function countArticleLivingThings()
    {
        return $this->createQueryBuilder('a')
            ->select('count(a.id) as nbrArticles')
            ->leftJoin("a.articleLivingThing", "aLT")
            ->leftJoin("aLT.livingThing", "l")
            ->andWhere('l.id IS NOT NULL')
            ->getQuery()
            ->getSingleResult()["nbrArticles"];
    }

    /**
     * Count articles of living thing type approved (by the staff) in the database
     * 
     * @return int the number of article
     */
    public function countArticleLivingThingsApproved()
    {
        return $this->createQueryBuilder('a')
            ->select('count(a.id) as nbrArticles')
            ->leftJoin("a.articleLivingThing", "aLT")
            ->leftJoin("aLT.livingThing", "l")
            ->where('a.approved = 1')
            ->andWhere('l.id IS NOT NULL')
            ->getQuery()
            ->getSingleResult()["nbrArticles"]
        ;
    }

    /**
     * Count articles of living things approved from a specific kingdom (Bacteria, Archaea, Protozoa, Chromista, Plantae, Fungi or Animalia)
     * 
     * @param string kingdom of the living thing
     * @param int limit Parameter limit is the number of element per page
     * @return ArticleLivingThings
     */
    public function countArticleLivingThingsByKingdom($kingdom, $limit)
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a.id) / :limit as nbrOffset')
            ->leftJoin("a.articleLivingThing", "aLT")
            ->leftJoin("aLT.livingThing", "l")
            ->where('l.kingdom = :kingdom')
            ->andWhere('a.approved = 1')
            ->setParameter('limit', $limit)
            ->setParameter('kingdom', $kingdom)
            ->getQuery()
            ->getSingleResult()["nbrOffset"]
        ;
    }

    /**
     * Count all articles of elements approved (by the staff) of the database. The article, the articleElement and the element need to exist and
     * linked between then to include then in the count
     * 
     * @return int number of articles of element approved
     */
    public function countArticleElements()
    {
        return $this->createQueryBuilder('a')
            ->select('count(a.id) as nbrElements')
            ->leftJoin("a.articleElement", "aE")
            ->leftJoin("aE.element", "e")
            ->where('a.approved = 1')
            ->andWhere("e.id IS NOT NULL")
            ->getQuery()
            ->getSingleResult()["nbrElements"]
        ;
    }

    /**
     * Count articles of element type approved (by the staff) in the database
     * 
     * @return int the number of article
     */
    public function countArticleElementsApproved()
    {
        return $this->createQueryBuilder('a')
            ->select('count(a.id) as nbrArticles')
            ->leftJoin("a.articleElement", "aE")
            ->leftJoin("aE.element", "e")
            ->where('a.approved = 1')
            ->andWhere('e.id IS NOT NULL')
            ->getQuery()
            ->getSingleResult()["nbrArticles"]
        ;
    }

    /**
     * Count all articles of minerals approved (by the staff) of the database
     * 
     * @return int number of articles of mineral approved
     */
    public function countArticleMinerals()
    {
        return $this->createQueryBuilder('a')
            ->select('count(a.id) as nbrMinerals')
            ->leftJoin("a.articleMineral", "aM")
            ->leftJoin("aM.mineral", "m")
            ->where('a.approved = 1')
            ->andWhere("m.id IS NOT NULL")
            ->getQuery()
            ->getSingleResult()["nbrMinerals"]
        ;
    }

    /**
     * Count articles of mineral type approved (by the staff) in the database
     * 
     * @return int the number of article
     */
    public function countArticleMineralsApproved()
    {
        return $this->createQueryBuilder('a')
            ->select('count(a.id) as nbrArticles')
            ->leftJoin("a.articleMineral", "aM")
            ->leftJoin("aM.element", "m")
            ->where('a.approved = 1')
            ->andWhere('aM.id IS NOT NULL')
            ->andWhere('m.id IS NOT NULL')
            ->getQuery()
            ->getSingleResult()["nbrArticles"]
        ;
    }
}
