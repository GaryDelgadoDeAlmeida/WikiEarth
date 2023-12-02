<?php

namespace App\Manager;

use App\Entity\Element;
use Symfony\Component\Form\Form;
use App\Repository\ElementRepository;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ElementManager extends AbstractController {

    private ElementRepository $elementRepository;
    
    function __construct(ContainerInterface $container, ElementRepository $elementRepository)
    {
        $this->setContainer($container);
        $this->elementRepository = $elementRepository;
    }

    public function setElement(UploadedFile $mediaFile = null, Element &$element, Form $formElement)
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

        $element->setCreatedAt(new \DateTime());
        $element->setVolumicMass(explode(" || ", $formElement["volumicMass"]->getData()));
        $this->elementRepository->save($element, true);

        return [
            "error" => false,
            "class" => "success",
            "message" => "L'insertion/mise à jour a bien été prise en compte"
        ];
    }
}