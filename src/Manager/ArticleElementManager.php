<?php

namespace App\Manager;

use App\Entity\{ArticleElement, Element};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleElementManager extends AbstractController {
    
    public function setArticleElement(ArticleElement &$articleElement, Element $element, EntityManagerInterface $manager, $user = null)
    {
        // On effectue d'abord l'insertion
        $articleElement->setTitle($element->getName());
        $articleElement->setApproved(false);
        $articleElement->setCreatedAt(new \DateTime());
        $manager->persist($articleElement);
        $manager->flush();
        $manager->clear();

        if(!empty($user)) {
            $articleElement->setUser($user);
        }

        $articleElement->setElement($element);
        $manager->merge($articleElement);
        $manager->flush();

        return [
            "error" => false,
            "class" => "success",
            "message" => "The article {$element->getName()} has been registed"
        ];
    }
}