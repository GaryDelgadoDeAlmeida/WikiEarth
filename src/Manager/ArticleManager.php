<?php

namespace App\Manager;

use App\Entity\{User, Article, ArticleLivingThing, ArticleElement, ArticleMineral};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;

class ArticleManager extends AbstractController {

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }
    
    /**
     * @param object content of the article
     * @param EntityManagerInterface doctrine manager for insertion into database
     * @param User user to link article
     * @return array return of the process
     */
    public function insertArticle($articleItem, User $user)
    {
        try {
            $article = new Article();
        
            if($articleItem instanceof ArticleElement) {
                $article->setTitle($articleItem->getElement()->getName());
                $article->setArticleElement($articleItem);
            } elseif($articleItem instanceof ArticleMineral) {
                $article->setTitle($articleItem->getMineral()->getName());
                $article->setArticleMineral($articleItem);
            } elseif($articleItem instanceof ArticleLivingThing) {
                $article->setTitle($articleItem->getMineral()->getName());
                $article->setArticleLivingThing($articleItem);
            } else {
                throw new \Exception("The instance of the article isn't allowed");
            }

            $article->setApproved(false);
            $article->setUser($user);
            $article->setUpdatedAt(new \DateTime());
            $article->setCreatedAt(new \DateTime());
            $this->em->merge($article);
            $this->em->flush();
            $this->em->clear();

            return [
                "error" => false,
                "class" => "success",
                "message" => "Le nouvel article {$article->getTitle()} a bien été ajouté.",
            ];
        } catch(\Exception $e) {
            return [
                "error" => true,
                "class" => "danger",
                "message" => "Hmm, an error occurred. A notification has been send to the admin to check what the problem.",
            ];
        }
    }

    /**
     * @param object content of the article
     * @param EntityManagerInterface doctrine manager for insertion into database
     * @param User user to link article
     * @return array return of the process
     */
    public function setArticle($articleItem, User $user)
    {
        return [];
    }
}