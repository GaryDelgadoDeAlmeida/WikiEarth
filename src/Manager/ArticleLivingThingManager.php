<?php

namespace Manager;

use App\Entity\LivingThing;
use App\Entity\ArticleLivingThing;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleLivingThingManager extends AbstractController {

    public function setArticleLivingThing(ArticleLivingThing $articleLivingThing, LivingThing $livingThing, EntityManagerInterface $manager, $user = null)
    {
        $manager->clear();

        if(!empty($user)) {
            $articleLivingThing->setUser($user);
        }

        $livingThing->setArticleLivingThing($articleLivingThing);
        $articleLivingThing->setApproved(false);
        $articleLivingThing->setCreatedAt(new \DateTime());

        $manager->merge($articleLivingThing);
        $manager->flush();
        $manager->clear();

        // return $articleLivingThing;
    }
}