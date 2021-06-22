<?php

namespace App\Manager;

use App\Entity\{ArticleElement, Element};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleElementManager extends AbstractController {
    
    public function setArticleElement(ArticleElement &$articleElement, Element $element, EntityManagerInterface $manager)
    {
        // On effectue d'abord l'insertion
        $articleElement->setCreatedAt(new \DateTime());
        if($articleElement->getId() != null) {
            $manager->merge($articleElement);
        } else {
            $manager->persist($articleElement);
        }
        $manager->flush();
        $manager->clear();

        $element->setArticleElement($articleElement);
        $manager->merge($articleElement);
        $manager->flush();

        return [
            "error" => false,
            "class" => "success",
            "message" => "The article {$element->getName()} has been registed"
        ];
    }
}