<?php

namespace App\Manager;

use App\Entity\{Mineral, MediaGallery, ArticleMineral};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleMineralManager extends AbstractController {

    private EntityManagerInterface $em;
    
    function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }
    
    /**
     * Insert the content of the article and mineral into the databse
     * 
     * @param ArticleMineral contenu de l'article
     * @param Mineral the mineral concerned by the creation of the article
     * @param EntityManagerInterface the manager object used to communicate with the bdd
     * @return array return the status of the process
     */
    public function setArticleMineral(ArticleMineral &$articleMineral, Mineral $mineral)
    {
        // On effectue d'abord l'insertion
        $articleMineral->setCreatedAt(new \DateTime());
        if($articleMineral->getId() != null) {
            $this->em->merge($articleMineral);
        } else {
            $this->em->persist($articleMineral);
        }
        $this->em->flush();
        $this->em->clear();

        $mineral->setArticleMineral($articleMineral);
        $this->em->merge($articleMineral);
        $this->em->flush();

        return [
            "error" => false,
            "class" => "success",
            "message" => "Le nouvel article {$mineral->getName()} a bien été ajouté.",
        ];
    }
}