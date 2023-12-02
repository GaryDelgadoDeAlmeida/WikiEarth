<?php

namespace App\Manager;

use App\Entity\Mineral;
use Symfony\Component\Form\Form;
use App\Repository\MineralRepository;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MineralManager extends AbstractController {

    private MineralRepository $mineralRepository;
    
    function __construct(ContainerInterface $container, MineralRepository $mineralRepository)
    {
        $this->setContainer($container);
        $this->mineralRepository = $mineralRepository;
    }

    public function setMineral(UploadedFile $mediaFile = null, Mineral &$mineral, Form $formMineral)
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
        
        $this->mineralRepository->save($mineral, true);
        
        return [
            "error" => false,
            "class" => "success",
            "message" => "L'enregistrement du mineral {$mineral->getName()} a bien été pris en compte",
        ];
    }
}