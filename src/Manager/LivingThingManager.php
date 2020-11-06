<?php

namespace Manager;

use App\Entity\LivingThing;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LivingThingManager extends AbstractController {

    function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    public function setLivingThing(UploadedFile $mediaFile = null, LivingThing &$livingThing, EntityManagerInterface $manager)
    {
        if($mediaFile) {
            $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $livingThing->getName() . '.' . $mediaFile->guessExtension();
            $kingdomDirectory = $this->getKingdomDirectory(ucfirst(strtolower($livingThing->getKingdom())));

            if(!file_exists($kingdomDirectory)) {
                mkdir($kingdomDirectory, 0777, true);
            }

            try {
                if(
                    array_search(
                        $kingdomDirectory . $newFilename, 
                        glob($kingdomDirectory . "*." . $mediaFile->guessExtension())
                    )
                ) {
                    unlink($kingdomDirectory . $newFilename);
                }
                
                $mediaFile->move(
                    $kingdomDirectory,
                    $newFilename
                );
            } catch (FileException $e) {
                dd($e->getMessage());
            }

            $livingThing->setImgPath("content/wikiearth/living-thing/" . $this->convertKingdomClassification(ucfirst(strtolower($livingThing->getKingdom()))) . "/{$newFilename}");
        }

        $manager->persist($livingThing);
        $manager->flush();
        $manager->clear();

        // return $livingThing;
    }

    public function getKingdomDirectory($kingdomClassification)
    {
        $kingdomPath = "";
        
        if($kingdomClassification == "Animalia") {
            $kingdomPath = $this->getParameter('project_living_thing_animals_dir');
        } elseif($kingdomClassification == "Plantae") {
            $kingdomPath = $this->getParameter('project_living_thing_plants_dir');
        } elseif($kingdomClassification == "Fungi") {
            $kingdomPath = $this->getParameter('project_living_thing_fungis_dir');
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
        } elseif($kingdomClassification == "Fungi") {
            $kingdom = "fungis";
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