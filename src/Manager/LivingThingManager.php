<?php

namespace App\Manager;

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
        try {
            if($mediaFile) {
                $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $livingThing->getName() . '.' . $mediaFile->guessExtension();
                $kingdomDirectory = $this->getKingdomDirectory(ucfirst(strtolower($livingThing->getKingdom())));
    
                // Si la classification n'est pas une classification valide alors ...
                if(empty($kingdomDirectory)) {
                    throw new \Exception("La classification {$livingThing->getKingdom()} n'est pas autorisée.");
                }
    
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
                    throw new $e->getMessage();
                }
    
                $livingThing->setImgPath("content/wikiearth/living-thing/" . ucfirst(strtolower($livingThing->getKingdom())) . "/{$newFilename}");
            }
    
            $manager->persist($livingThing);
            $manager->flush();
            $manager->clear();
            
            return [
                "error" => false,
                "class" => "success",
                "message" => "L'ajout de l'être vivant {$livingThing->getName()} a correctement été ajouté"
            ];

        } catch(\Exception $e) {
            return [
                "error" => true,
                "class" => "danger",
                "message" => $e->getMessage()
            ];
        }
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
        } elseif($kingdomClassification == "Bacteria") {
            $kingdomPath = $this->getParameter('project_living_thing_bacteria_dir');
        } elseif($kingdomClassification == "Archaea") {
            $kingdomPath = $this->getParameter('project_living_thing_archaea_dir');
        } elseif($kingdomClassification == "Protozoa") {
            $kingdomPath = $this->getParameter('project_living_thing_protozoa_dir');
        } elseif($kingdomClassification == "Chromista") {
            $kingdomPath = $this->getParameter('project_living_thing_chromista_dir');
        }

        return $kingdomPath;
    }
}