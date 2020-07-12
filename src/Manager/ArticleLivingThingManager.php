<?php

namespace Manager;

use App\Entity\LivingThing;
use App\Entity\ArticleLivingThing;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class ArticleLivingThingManager {

    public function insertArticleLivingThing(Form $formRequest, Request $request, EntityManagerInterface $manager, $project_wikiearth_dir, $user)
    {
        $mediaLivingThingFile = isset($request->files->get('article_living_thing')['livingThing']['imgPath']) ? $request->files->get('article_living_thing')['livingThing']['imgPath'] : null;
        $formRequest = $request->get('article_living_thing');
        
        $livingThing = new LivingThing();
        $livingThing->setCommonName($formRequest["livingThing"]['commonName']);
        $livingThing->setName($formRequest["livingThing"]['name']);
        $livingThing->setKingdom($formRequest["livingThing"]['kingdom']);
        $livingThing->setSubKingdom($formRequest["livingThing"]['subKingdom']);
        $livingThing->setDomain($formRequest["livingThing"]['domain']);
        $livingThing->setBranch($formRequest["livingThing"]['branch']);
        $livingThing->setSubBranch($formRequest["livingThing"]['subBranch']);
        $livingThing->setInfraBranch($formRequest["livingThing"]['infraBranch']);
        $livingThing->setDivision($formRequest["livingThing"]['division']);
        $livingThing->setSuperClass($formRequest["livingThing"]['superClass']);
        $livingThing->setClass($formRequest["livingThing"]['class']);
        $livingThing->setSubClass($formRequest["livingThing"]['subClass']);
        $livingThing->setInfraClass($formRequest["livingThing"]['infraClass']);
        $livingThing->setSuperOrder($formRequest["livingThing"]['superOrder']);
        $livingThing->setNormalOrder($formRequest["livingThing"]['normalOrder']);
        $livingThing->setSubOrder($formRequest["livingThing"]['subOrder']);
        $livingThing->setInfraOrder($formRequest["livingThing"]['infraOrder']);
        $livingThing->setMicroOrder($formRequest["livingThing"]['microOrder']);
        $livingThing->setSuperFamily($formRequest["livingThing"]['superFamily']);
        $livingThing->setFamily($formRequest["livingThing"]['family']);
        $livingThing->setSubFamily($formRequest["livingThing"]['subFamily']);
        $livingThing->setGenus($formRequest["livingThing"]['genus']);
        $livingThing->setSubGenus($formRequest["livingThing"]['subGenus']);
        $livingThing->setSpecies($formRequest["livingThing"]['species']);
        $livingThing->setSubSpecies($formRequest["livingThing"]['subSpecies']);
        
        if($mediaLivingThingFile != null) {
            $originalFilename = pathinfo($mediaLivingThingFile->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $newFilename = $livingThing->getName() . '.' . $mediaLivingThingFile->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                if(
                    array_search(
                        $project_wikiearth_dir . $livingThing->getKingdom() . "/img/" . $livingThing->getName() . "/" . $newFilename, 
                        glob($project_wikiearth_dir . $livingThing->getKingdom() . "/img/" . $livingThing->getName() . "/*." . $mediaLivingThingFile->guessExtension())
                    )
                ) {
                    unlink($project_wikiearth_dir . $livingThing->getKingdom() . "/img/" . $livingThing->getName() . "/" . $newFilename);
                }
                
                $mediaLivingThingFile->move(
                    $project_wikiearth_dir . $livingThing->getKingdom() . "/img/" . $livingThing->getName(),
                    $newFilename
                );
                
                $livingThing->setImgPath($project_wikiearth_dir . $livingThing->getKingdom() . "/img/" . $livingThing->getName() . "/" . $newFilename);
            } catch (FileException $e) {
                die($e->getMessage());
            }
        }
        
        $article = new ArticleLivingThing();
        $article->setUser($user);
        $article->setIdLivingThing($livingThing);
        $article->setApproved(false);
        $article->setCreatedAt(new \DateTime());

        $manager->persist($livingThing);
        $manager->persist($article);
        $manager->flush();
    }

    public function setArticleLivingThing(ArticleLivingThing $articleLivingThing, LivingThing $livingThing, EntityManagerInterface $manager, $user)
    {
        $articleLivingThing->setUser($user);
        $articleLivingThing->setIdLivingThing($livingThing);
        $articleLivingThing->setApproved(false);
        $articleLivingThing->setCreatedAt(new \DateTime());

        $manager->persist($livingThing);
        $manager->persist($articleLivingThing);
        $manager->flush();
    }
}