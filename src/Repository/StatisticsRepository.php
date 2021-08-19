<?php

namespace App\Repository;

use App\Entity\Statistics;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Statistics|null find($id, $lockMode = null, $lockVersion = null)
 * @method Statistics|null findOneBy(array $criteria, array $orderBy = null)
 * @method Statistics[]    findAll()
 * @method Statistics[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatisticsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Statistics::class);
    }

    /**
     * Get statistics of a specific month
     * 
     * @param string year
     * @param string month
     * @return Statistics
     */
    public function getStatisticsByMonth(string $year, string $month)
    {
        return $this->createQueryBuilder("s")
            ->where("YEAR(s.createdAt) = :year")
            ->andWhere("MONTH(s.createdAt) = :month")
            ->setParameters([
                "year" => $year, 
                "month" => $month
            ])
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Get statistics of a specific year
     * 
     * @param string year
     * @return Statistics
     */
    public function getStatisticsByYear(string $year)
    {
        return $this->createQueryBuilder("s")
            ->where("YEAR(s.createdAt) = :year")
            ->setParameter("year", $year)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Increment anonymous connection
     * 
     * @param string year
     * @param string month
     * @return mixed
     */
    public function incrementAnonymousConnection(string $year, string $month)
    {
        return $this->createQueryBuilder("s")
            ->update()
            ->set('s.nbrAnonymousConnection', 's.nbrAnonymousConnection + 1')
            ->where("YEAR(s.createdAt) = :year")
            ->andWhere("MONTH(s.createdAt) = :month")
            ->setParameters([
                "year" => $year, 
                "month" => $month
            ])
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * Increment authentified user
     * 
     * @param string year
     * @param string month
     * @return mixed
     */
    public function incrementUserConnection(string $year, string $month)
    {
        return $this->createQueryBuilder("s")
            ->update()
            ->set('s.nbrUsersConnection', 's.nbrUsersConnection + 1')
            ->where("YEAR(s.createdAt) = :year")
            ->andWhere("MONTH(s.createdAt) = :month")
            ->setParameters([
                "year" => $year, 
                "month" => $month
            ])
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * Increment authentified user
     * 
     * @param string year
     * @param string month
     * @return mixed
     */
    public function incrementPageConsultation(string $year, string $month)
    {
        return $this->createQueryBuilder("s")
            ->update()
            ->set('s.nbrPageConsultations', 's.nbrPageConsultations + 1')
            ->where("YEAR(s.createdAt) = :year")
            ->andWhere("MONTH(s.createdAt) = :month")
            ->setParameters([
                "year" => $year, 
                "month" => $month
            ])
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * Increment authentified user
     * 
     * @param string year
     * @param string month
     * @return mixed
     */
    public function incrementArticleCreation(string $year, string $month)
    {
        return $this->createQueryBuilder("s")
            ->update()
            ->set('s.nbrArticleCreations', 's.nbrArticleCreations + 1')
            ->where("YEAR(s.createdAt) = :year")
            ->andWhere("MONTH(s.createdAt) = :month")
            ->setParameters([
                "year" => $year, 
                "month" => $month
            ])
            ->getQuery()
            ->execute()
        ;
    }
}
