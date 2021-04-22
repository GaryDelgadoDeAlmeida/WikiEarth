<?php

namespace App\Manager;

use App\Entity\Element;
use Symfony\Component\Form\Form;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ElementManager extends AbstractController {
    
    function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    public function setElement(UploadedFile $mediaFile = null, Element &$element, Form $formElement, EntityManagerInterface $manager)
    {
        if(!empty($mediaFile)) {
            $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $element->getName() . '.' . $mediaFile->guessExtension();
            $elementDirectory = $this->getParameter('project_natural_elements_elements_dir');

            if(!file_exists($elementDirectory)) {
                mkdir($elementDirectory, 0777, true);
            }

            try {
                if(
                    array_search(
                        $elementDirectory . $newFilename, 
                        glob($elementDirectory . "*." . $mediaFile->guessExtension())
                    )
                ) {
                    unlink($elementDirectory . $newFilename);
                }
                
                $mediaFile->move(
                    $elementDirectory,
                    $newFilename
                );
            } catch (FileException $e) {
                throw new $e->getMessage();
            }

            $element->setImgPath("content/wikiearth/natural-elements/elements/{$newFilename}");
        }

        $element->setVolumicMass(explode(" || ", $formElement["volumicMass"]->getData()));
        $manager->persist($element);
        $manager->flush();
        $manager->clear();

        return [
            "error" => false,
            "class" => "success",
            "message" => "L'insertion/mise à jour a bien été prise en compte"
        ];
    }
}