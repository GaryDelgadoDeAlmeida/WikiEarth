<?php

namespace Manager;

use App\Entity\LivingThing;
use App\Entity\ArticleLivingThing;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleLivingThingManager extends AbstractController {

    public function setArticleLivingThing(ArticleLivingThing $articleLivingThing, LivingThing $livingThing, EntityManagerInterface $manager, $user = null)
    {
        if(!empty($user)) {
            $articleLivingThing->setUser($user);
        }

        $articleLivingThing->setIdLivingThing($livingThing);
        $articleLivingThing->setApproved(false);
        $articleLivingThing->setCreatedAt(new \DateTime());

        $manager->persist($livingThing);
        $manager->persist($articleLivingThing);
        $manager->flush();
        $manager->clear();
    }
}