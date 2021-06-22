<?php

namespace App\Manager;

use App\Entity\{LivingThing, MediaGallery, ArticleLivingThing};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleLivingThingManager extends AbstractController {

    public function setArticleLivingThing(ArticleLivingThing &$articleLivingThing, LivingThing $livingThing, EntityManagerInterface $manager, $user = null)
    {
        // On effectue d'abord l'insertion
        $livingThing->setArticleLivingThing($articleLivingThing);
        $articleLivingThing->setCreatedAt(new \DateTime());
        $manager->merge($articleLivingThing);
        $manager->flush();
        $manager->clear();
        
        return [
            "error" => false,
            "class" => "success",
            "message" => "L'article {$livingThing->getName()} a bien été ajouté."
        ];
    }
}