<?php

namespace App\Manager;

use App\Entity\Mineral;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MineralManager extends AbstractController {
    
    function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    public function setMineral(UploadedFile $mediaFile = null, Mineral &$mineral, EntityManagerInterface $manager)
    {
        if(!empty($mediaFile)) {
            // TODO : traitement logique métier du système de upload du média
        }

        $mineral->setImaStatus(explode(",", $mineral->getImaStatus()));
        $manager->persist($mineral);
        $manager->flush();
        $manager->clear();
    }
}