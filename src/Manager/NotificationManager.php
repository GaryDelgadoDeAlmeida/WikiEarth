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
}