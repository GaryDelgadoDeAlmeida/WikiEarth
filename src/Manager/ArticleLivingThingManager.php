<?php

namespace Manager;

use App\Entity\LivingThing;
use Symfony\Component\Form\Form;
use App\Entity\ArticleLivingThing;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleLivingThingManager extends AbstractController {

    private $livingThingManager;

    function __construct()
    {
        $this->livingThingManager = new LivingThingManager();
    }

    public function insertArticleLivingThing(Form $formRequest, ArticleLivingThing $article, EntityManagerInterface $manager, $project_wikiearth_dir, $user)
    {
        $mediaLivingThingFile = !empty($formRequest["livingThing"]["imgPath"]->getData()) ? $formRequest["livingThing"]["imgPath"]->getData() : null;
        $livingThing = $formRequest["livingThing"]->getData();

        // $this->livingThingManager->setLivingThing($mediaLivingThingFile, $livingThing, $manager);
        
        if(!empty($mediaLivingThingFile)) {
            $originalFilename = pathinfo($mediaLivingThingFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $livingThing->getName() . '.' . $mediaLivingThingFile->guessExtension();

            try {
                if(
                    array_search(
                        $project_wikiearth_dir . "living-thing/" . $livingThing->getKingdom() . "/img/" . $livingThing->getName() . "/" . $newFilename, 
                        glob($project_wikiearth_dir . "living-thing/" . $livingThing->getKingdom() . "/img/" . $livingThing->getName() . "/*." . $mediaLivingThingFile->guessExtension())
                    )
                ) {
                    unlink($project_wikiearth_dir . "living-thing/" . $livingThing->getKingdom() . "/img/" . $livingThing->getName() . "/" . $newFilename);
                }
                
                $mediaLivingThingFile->move(
                    $project_wikiearth_dir . "living-thing/" . $livingThing->getKingdom() . "/img/" . $livingThing->getName(),
                    $newFilename
                );
                
                $livingThing->setImgPath("content/wikiearth/living-thing" . $livingThing->getKingdom() . "/img/" . $livingThing->getName() . "/" . $newFilename);
            } catch (FileException $e) {
                die($e->getMessage());
            }
        }

        if(!empty($livingThing->getCountries())) {
            foreach($livingThing->getCountries() as $oneCountry) {
                $oneCountry->addLivingThing($livingThing);
                $manager->persist($oneCountry);
            }
        }
        
        $article->setUser($user);
        $article->setIdLivingThing($livingThing);
        $article->setApproved(false);
        $article->setCreatedAt(new \DateTime());
        $manager->persist($livingThing);
        $manager->persist($article);
        $manager->flush();
    }

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
    }
}