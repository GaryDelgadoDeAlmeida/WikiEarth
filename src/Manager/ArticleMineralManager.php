<?php

namespace App\Manager;

use App\Entity\{Mineral, MediaGallery, ArticleMineral};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleMineralManager extends AbstractController {
    
    /**
     * Insert the content of the article and mineral into the databse
     * 
     * @param ArticleMineral contenu de l'article
     * @param Mineral the mineral concerned by the creation of the article
     * @param EntityManagerInterface the manager object used to communicate with the bdd
     * @return array return the status of the process
     */
    public function setArticleMineral(ArticleMineral &$articleMineral, Mineral $mineral, EntityManagerInterface $manager)
    {
        // On effectue d'abord l'insertion
        $articleMineral->setCreatedAt(new \DateTime());
        if($articleMineral->getId() != null) {
            $manager->merge($articleMineral);
        } else {
            $manager->persist($articleMineral);
        }
        $manager->flush();
        $manager->clear();

        $mineral->setArticleMineral($articleMineral);
        $manager->merge($articleMineral);
        $manager->flush();

        return [
            "error" => false,
            "class" => "success",
            "message" => "Le nouvel article {$mineral->getName()} a bien été ajouté.",
        ];
    }
}