<?php

namespace App\Manager;

use App\Entity\Mineral;
use Symfony\Component\Form\Form;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MineralManager extends AbstractController {
    
    function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    public function setMineral(UploadedFile $mediaFile = null, Mineral &$mineral, Form $formMineral, EntityManagerInterface $manager)
    {
        if(!empty($mediaFile)) {
            $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $mineral->getName() . '.' . $mediaFile->guessExtension();
            $mineralDirectory = $this->getParameter('project_natural_elements_minerals_dir');

            if(!file_exists($mineralDirectory)) {
                mkdir($mineralDirectory, 0777, true);
            }

            try {
                if(
                    array_search(
                        $mineralDirectory . $newFilename, 
                        glob($mineralDirectory . "*." . $mediaFile->guessExtension())
                    )
                ) {
                    unlink($mineralDirectory . $newFilename);
                }
                
                $mediaFile->move(
                    $mineralDirectory,
                    $newFilename
                );
            } catch (FileException $e) {
                throw new $e->getMessage();
            }

            $mineral->setImgPath("content/wikiearth/natural-elements/minerals/{$newFilename}");
        }

        $mineral->setImaStatus(explode(",", $formMineral["imaStatus"]->getData()));
        $mineral->setCreatedAt(new \DateTime());
        $manager->persist($mineral);
        $manager->flush();
        $manager->clear();
        
        return [
            "error" => false,
            "class" => "success",
            "message" => "L'enregistrement du mineral {$mineral->getName()} a bien été pris en compte",
        ];
    }
}