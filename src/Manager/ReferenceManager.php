<?php

namespace App\Manager;

use App\Entity\ArticleLivingThing;
use Doctrine\ORM\EntityManagerInterface;

class ReferenceManager {

    public function setReferences(array $references, ArticleLivingThing &$articleLivingThing, EntityManagerInterface $manager)
    {
        foreach($references as $oneReference) {
            $oneReference->setCreatedAt(current_time('mysql'));
            $manager->persist($oneReference);
            $manager->flush();
            $manager->clear();

            $oneReference->setArticleLivingThing($articleLivingThing);
            $manager->merge($oneReference);
            $manager->flush();
        }
    }
}