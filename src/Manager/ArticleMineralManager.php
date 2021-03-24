<?php

namespace App\Manager;

use App\Entity\{Mineral, MediaGallery, ArticleMineral};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleMineralManager extends AbstractController {
    
    public function setArticleMineral(ArticleMineral &$articleMineral, Mineral $mineral, EntityManagerInterface $manager, $user = null)
    {
        // On effectue d'abord l'insertion
        $articleMineral->setTitle($mineral->getName());
        $articleMineral->setApproved(false);
        $articleMineral->setCreatedAt(new \DateTime());
        $manager->persist($articleMineral);
        $manager->flush();
        $manager->clear();

        if(!empty($user)) {
            $articleMineral->setUser($user);
        }

        $mineral->setArticleMineral($articleMineral);
        $manager->merge($articleMineral);
        $manager->flush();

        return [
            "error" => false,
            "class" => "success",
            "message" => "Le nouvel article {$articleMineral->getTitle()} a bien été ajouté.",
        ];
    }
}