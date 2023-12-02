<?php

namespace App\Manager;

use App\Entity\{ArticleElement, Element};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleElementManager extends AbstractController {

    private EntityManagerInterface $em;

    function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }
    
    public function setArticleElement(ArticleElement &$articleElement, Element $element)
    {
        // On effectue d'abord l'insertion
        $articleElement->setCreatedAt(new \DateTime());
        if($articleElement->getId() != null) {
            $this->em->merge($articleElement);
        } else {
            $this->em->persist($articleElement);
        }
        $this->em->flush();
        $this->em->clear();

        $element->setArticleElement($articleElement);
        $this->em->merge($articleElement);
        $this->em->flush();

        return [
            "error" => false,
            "class" => "success",
            "message" => "The article {$element->getName()} has been registed"
        ];
    }
}