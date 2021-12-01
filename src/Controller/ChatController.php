<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
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
    public function index(Request $request): Response
    {
        $startedDiscussions = [
            1 => [
                "id" => 1,
                "firstname" => "Alban",
                "lastname" => "DUPONT",
                "image" => "content/users/avatar.png",
                "lastMessage" => "Fine ?",
                "isCurrent" => false,
            ],
            2 => [
                "id" => 2,
                "firstname" => "Jack",
                "lastname" => "Parkson",
                "image" => "content/users/avatar.png",
                "lastMessage" => "Ok",
                "isCurrent" => false
            ],
            3 => [
                "id" => 3,
                "firstname" => "Robert",
                "lastname" => "Johnson",
                "image" => "content/users/avatar.png",
                "lastMessage" => "No, That's wrong !",
                "isCurrent" => false
            ],
            4 => [
                "id" => 4,
                "firstname" => "Michael",
                "lastname" => "Johnson",
                "image" => "content/users/avatar.png",
                "lastMessage" => "This is abernathyite, a beautiful mineral !",
                "isCurrent" => false
            ]
        ];

        $user = $request->get("user");
        if(!empty($user)) {
            $startedDiscussions[$request->get("user")]["isCurrent"] = true;
        }

        return $this->render('chat/index.html.twig', [
            "startedDiscussions" => $startedDiscussions,
        ]);
    }
}
