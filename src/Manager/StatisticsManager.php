<?php

namespace App\Manager;

use App\Entity\Statistics;
use Doctrine\ORM\EntityManagerInterface;

class StatisticsManager {

    private $em;
    private $statisticsRepo;

    function __construct(EntityManagerInterface $manager)
    {
        $this->em = $manager;
        $this->statisticsRepo = $manager->getRepository(Statistics::class);
    }

    /**
     * update anonymous connection statistics
     */
    public function updateAnonymousConnectionStatistics()
    {
        // If there is a existing stat than ...
        $currentDate = $this->checkIfStatExist();
        
        if(!empty($currentDate))
            $this->statisticsRepo->incrementAnonymousConnection($currentDate["currentYear"], $currentDate["currentMonth"]);
    }

    /**
     * update authentified user connection statistics
     */
    public function updateUserConnectionStatistics()
    {
        // If there is a existing stat than ...
        $currentDate = $this->checkIfStatExist();
        
        if(!empty($currentDate))
            $this->statisticsRepo->incrementUserConnection($currentDate["currentYear"], $currentDate["currentMonth"]);
    }

    /**
     * update article consultation statistics
     */
    public function updateArticlePageConsultationsStatistics()
    {
        // If there is a existing stat than ...
        $currentDate = $this->checkIfStatExist();
        
        if(!empty($currentDate))
            $this->statisticsRepo->incrementPageConsultation($currentDate["currentYear"], $currentDate["currentMonth"]);
    }

    /**
     * update article creation statistics
     */
    public function updateArticleCreationsStatistics()
    {
        // If there is a existing stat than ...
        $currentDate = $this->checkIfStatExist();
        
        if(!empty($currentDate))
            $this->statisticsRepo->incrementArticleCreation($currentDate["currentYear"], $currentDate["currentMonth"]);
    }

    /**
     * Check if a stat exist into the database
     * 
     * @return array array of date (year and month)
     */
    public function checkIfStatExist()
    {
        $currentDate = new \DateTime();
        $currentYear = $currentDate->format("Y");
        $currentMonth = $currentDate->format("m");
        $existingStatistics = true;

        // If a stats of the current month and year not exist than create it
        if(empty($this->statisticsRepo->getStatisticsByMonth($currentYear, $currentMonth))) {
            $existingStatistics = !empty($this->createStatistics()) ? true : false;
        }
        
        // If there is a stat ( if existingStatistics != true)
        if(!$existingStatistics) {
            return [];
        }

        return ["currentYear" => $currentYear, "currentMonth" => $currentMonth];
    }

    /**
     * Create a new stats into bdd
     * 
     * @return Statistics
     */
    public function createStatistics()
    {
        try {
            $statistics = new Statistics();
            $statistics->setNbrAnonymousConnection(0);
            $statistics->setNbrUsersConnection(0);
            $statistics->setNbrArticleCreations(0);
            $statistics->setNbrPageConsultations(0);
            $statistics->setCreatedAt(new \DateTimeImmutable());

            $this->em->persist($statistics);
            $this->em->flush();
            $this->em->clear();

            return $statistics;
        } catch(\Exception $e) {
            return false;
        }
    }
}