<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 */
class ChatController extends AbstractController
{
    private $current_logged_user;
    private $manager;

    function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $manager)
    {
        $this->current_logged_user = $tokenStorage->getToken()->getUser();
        $this->manager = $manager;
    }

    /**
     * @Route("/user/chat", name="userChat")
     * @Route("/admin/chat", name="adminChat")
     */
    public function index(): Response
    {
        return $this->render('chat/index.html.twig', [
            'controller_name' => 'ChatController',
        ]);
    }
}
