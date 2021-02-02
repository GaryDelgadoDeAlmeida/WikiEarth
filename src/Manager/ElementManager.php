<?php

namespace App\Manager;

use App\Entity\Element;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ElementManager extends AbstractController {
    
    function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    public function setElement(UploadedFile $mediaFile = null, Element &$element, EntityManagerInterface $manager)
    {
        if(!empty($mediaFile)) {
            // TODO : traitement logique métier du système de upload du média
        }

        $manager->persist($element);
        $manager->flush();
        $manager->clear();
    }
}