<?php

namespace App\Manager;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ArticleLivingThingRepository;
use App\Entity\{LivingThing, MediaGallery, ArticleLivingThing};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleLivingThingManager extends AbstractController {

    private ArticleLivingThingRepository $articleLivingThingRepository;

    public function __construct(ArticleLivingThingRepository $articleLivingThingRepository) {
        $this->articleLivingThingRepository = $articleLivingThingRepository;
    }

    /**
     * @param ArticleLivingThing
     * @param LivingThing
     * @param EntityManagerInterface
     */
    public function setArticleLivingThing(
        ArticleLivingThing &$articleLivingThing, 
        LivingThing $livingThing, 
        $user = null
    ) {
        // On effectue d'abord l'insertion
        $livingThing->setArticleLivingThing($articleLivingThing);
        $articleLivingThing->setCreatedAt(new \DateTime());

        $this->articleLivingThingRepository->save($articleLivingThing, true);
        
        return [
            "error" => false,
            "class" => "success",
            "message" => "L'article {$livingThing->getName()} a bien été ajouté."
        ];
    }
}