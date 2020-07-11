<?php

namespace Manager;

use App\Entity\LivingThing;
use Symfony\Component\Form\Form;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LivingThingManager {

    public function setLivingThing(Form $formAnimal, LivingThing $livingThing, EntityManagerInterface $manager, $project_wikiearth_dir)
    {
        $mediaFile = $formAnimal['imgPath']->getData();
        if($mediaFile) {
            $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $newFilename = $livingThing->getName() . '.' . $mediaFile->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                if(
                    array_search(
                        $project_wikiearth_dir . $livingThing->getKingdom() . "/img/" . $livingThing->getName() . "/" . $newFilename, 
                        glob($project_wikiearth_dir . $livingThing->getKingdom() . "/img/" . $livingThing->getName() . "/*." . $mediaFile->guessExtension())
                    )
                ) {
                    unlink($project_wikiearth_dir . $livingThing->getKingdom() . "/img/" . $livingThing->getName() . "/" . $newFilename);
                }
                
                $mediaFile->move(
                    $project_wikiearth_dir . $livingThing->getKingdom() . "/img/" . $livingThing->getName(),
                    $newFilename
                );
            } catch (FileException $e) {
                dd($e->getMessage());
            }

            $livingThing->setImgPath($project_wikiearth_dir . $livingThing->getKingdom() . "/img/" . $livingThing->getName() . "/" . $newFilename);
        }

        $manager->persist($livingThing);
        $manager->flush();
    }
}