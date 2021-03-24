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
            // TODO : traitement logique métier du système de upload du média
        }

        $mineral->setImaStatus(explode(",", $formMineral["imaStatus"]->getData()));
        $mineral->setCreatedAt(new \DateTime());
        $manager->persist($mineral);
        $manager->flush();
        $manager->clear();
        
        return [
            "error" => false,
            "class" => "success",
            "message" => "Le nouveau mineral {$mineral->getName()} a bien été ajouté dans la base de données",
        ];
    }
}