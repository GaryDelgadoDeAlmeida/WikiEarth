<?php

namespace Manager;

use App\Entity\LivingThing;
use App\Entity\MediaGallery;
use App\Entity\ArticleLivingThing;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleLivingThingManager extends AbstractController {

    public function setArticleLivingThing(ArticleLivingThing &$articleLivingThing, LivingThing $livingThing, EntityManagerInterface $manager, $user = null)
    {
        // On effectue d'abord l'insertion
        $articleLivingThing->setApproved(false);
        $articleLivingThing->setCreatedAt(new \DateTime());
        $manager->persist($articleLivingThing);
        $manager->flush();
        $manager->clear();

        if(!empty($user)) {
            $articleLivingThing->setUser($user);
        }

        $livingThing->setArticleLivingThing($articleLivingThing);
        $manager->merge($articleLivingThing);
        $manager->flush();
    }
}