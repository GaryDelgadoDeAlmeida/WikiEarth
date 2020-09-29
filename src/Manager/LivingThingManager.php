<?php

namespace Manager;

use App\Entity\LivingThing;
use Symfony\Component\Form\Form;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LivingThingManager extends AbstractController {

    public function setLivingThing(Form $formAnimal, LivingThing $livingThing, EntityManagerInterface $manager)
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
                        $this->getKingdomDirectory(ucfirst(strtolower($livingThing->getKingdom()))) . $newFilename, 
                        glob($this->getKingdomDirectory(ucfirst(strtolower($livingThing->getKingdom()))) . "*." . $mediaFile->guessExtension())
                    )
                ) {
                    unlink($this->getKingdomDirectory(ucfirst(strtolower($livingThing->getKingdom()))) . $newFilename);
                }
                
                $mediaFile->move(
                    $this->getKingdomDirectory(ucfirst(strtolower($livingThing->getKingdom()))),
                    $newFilename
                );
            } catch (FileException $e) {
                dd($e->getMessage());
            }

            $livingThing->setImgPath("content/wikiearth/living-thing/" . $this->convertKingdomClassification(ucfirst(strtolower($livingThing->getKingdom()))) . "/{$newFilename}");
            dd($livingThing);
        }

        $manager->persist($livingThing);
        $manager->flush();
    }

    public function getKingdomDirectory($kingdomClassification)
    {
        $kingdomPath = "";
        
        if($kingdomClassification == "Animalia") {
            $kingdomPath = $this->getParameter('project_living_thing_animals_dir');
        } elseif($kingdomClassification == "Plantae") {
            $kingdomPath = $this->getParameter('project_living_thing_plants_dir');
        } elseif($kingdomClassification == "Insecta") {
            $kingdomPath = $this->getParameter('project_living_thing_insects_dir');
        } elseif($kingdomClassification == "Bacteria") {
            $kingdomPath = $this->getParameter('project_living_thing_bacteria_dir');
        } elseif($kingdomClassification == "Virae") {
            $kingdomPath = $this->getParameter('project_living_thing_virus_dir');
        }

        return $kingdomPath;
    }

    public function convertKingdomClassification($kingdomClassification)
    {
        $kingdom = "";
        
        if($kingdomClassification == "Animalia") {
            $kingdom = "animals";
        } elseif($kingdomClassification == "Plantae") {
            $kingdom = "plants";
        } elseif($kingdomClassification == "Insecta") {
            $kingdom = "insects";
        } elseif($kingdomClassification == "Bacteria") {
            $kingdom = "bacteria";
        } elseif($kingdomClassification == "Virae") {
            $kingdom = "virus";
        }

        return $kingdom;
    }
}