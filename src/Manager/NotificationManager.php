<?php

namespace App\Manager;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NotificationManager extends AbstractController {

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function livingThingNotFound($user)
    {
        $notification = new Notification();
        $notification->setUser($user);
        $notification->setType("info");
        $notification->setContent("The living thing you tried to edit don't exist");
        $notification->setCreatedAt(new \DateTime());
        $this->em->merge($notification);
        $this->em->flush();
        $this->em->clear();
    }

    public function livingThingAlreadyExist($user)
    {
        $notification = new Notification();
        $notification->setUser($user);
        $notification->setType("info");
        $notification->setContent("The living thing you tried to add already exist");
        $notification->setCreatedAt(new \DateTime());
        $this->em->merge($notification);
        $this->em->flush();
        $this->em->clear();
    }

    public function articleAlreadyExist($user)
    {
        $notification = new Notification();
        $notification->setUser($user);
        $notification->setType("info");
        $notification->setContent("This living thing already have an article");
        $notification->setCreatedAt(new \DateTime());
        $this->em->merge($notification);
        $this->em->flush();
        $this->em->clear();
    }
    
    /**
     * Un utilisateur crée un nouvel article
     */
    public function userCreateArticle($user)
    {
        $notification = new Notification();
        $notification->setUser($user);
        $notification->setType("info");
        $notification->setContent("You created an article. We'll check it");
        $notification->setCreatedAt(new \DateTime());
        $this->em->merge($notification);
        $this->em->flush();
        $this->em->clear();
    }

    public function userUpdateArticle($user)
    {
        $notification = new Notification();
        $notification->setUser($user);
        $notification->setType("info");
        $notification->setContent("You edited an article. We'll check it");
        $notification->setCreatedAt(new \DateTime());
        $this->em->merge($notification);
        $this->em->flush();
        $this->em->clear();
    }

    public function articleIsNowPublic($article)
    {
        $notfication = new Notification();
        $notfication->setUser($article->getUser());
        $notfication->setType("success");
        $notfication->setContent("The content of the article {$article->getTitle()} you writed is accurate. This article is now public.");
        $notfication->setCreatedAt(new \DateTime());
        $article->setApproved(true);
        $this->em->persist($article);
        $this->em->persist($notfication);
        $this->em->flush();
    }

    /**
     * L'administrateur a apprové la publication d'un article
     */
    public function adminApproveArticle($user)
    {
        $notification = new Notification();
        $notification->setUser($user);
        $notification->setType("success");
        $notification->setContent("Your publication has been accepted by the taff");
        $notification->setCreatedAt(new \DateTime());
        $this->em->merge($notification);
        $this->em->flush();
        $this->em->clear();
    }

    /**
     * L'administrateur a rejeté la publication d'un article (peut-être parce que le contenu n'était pas exact)
     */
    public function adminRefuseArticle($user)
    {
        $notification = new Notification();
        $notification->setUser($user);
        $notification->setType("danger");
        $notification->setContent("Your publication has been rejected by the taff");
        $notification->setCreatedAt(new \DateTime());
        $this->em->merge($notification);
        $this->em->flush();
        $this->em->clear();
    }
}