<?php

namespace App\Controller;

use App\Manager\ChatManager;
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
    private $manager;
    private $current_logged_user;
    private $chatManager;

    function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $manager)
    {
        $this->manager = $manager;
        $this->current_logged_user = $tokenStorage->getToken()->getUser();
        $this->chatManager = new ChatManager($this->manager);
    }

    /**
     * @Route("/user/chat", name="userChat")
     * @Route("/admin/chat", name="adminChat")
     */
    public function index(Request $request): Response
    {
        return $this->render("{$this->checkUserRole()}index.html.twig", $this->chatManager->prepareSideBar($request, $this->current_logged_user));
    }

    /**
     * @Route("/user/chat/discussion/add", name="userChatAddDiscussion")
     * @Route("/admin/chat/discussion/add", name="adminChatAddDiscussion")
     */
    public function add_discussion(Request $request)
    {
        return $this->render("{$this->checkUserRole()}add.html.twig", $this->chatManager->prepareSideBar($request, $this->current_logged_user));
    }

    /**
     * @Route("/user/chat/discussion/{discussion_id}/send", name="userChatSendDiscussion")
     * @Route("/admin/chat/discussion/{discussion_id}/send", name="adminChatSendDiscussion")
     */
    public function send_message_discussion(Request $request)
    {
        return $this->render('{$this->checkUserRole()}index.html.twig', $this->chatManager->prepareSideBar($request, $this->current_logged_user));
    }

    /**
     * @Route("/user/chat/discussion/{discussion_id}/delete", name="userChatDeleteDiscussion")
     * @Route("/admin/chat/discussion/{discussion_id}/delete", name="adminChatDeleteDiscussion")
     */
    public function delete_discussion(Request $request)
    {
        return $this->render("{$this->checkUserRole()}add.html.twig", $this->chatManager->prepareSideBar($request, $this->current_logged_user));
    }

    /**
     * @Route("/user/chat/discussion/{discussion_id}/download", name="userChatDownloadDiscussion")
     * @Route("/admin/chat/discussion/{discussion_id}/download", name="adminChatDownloadDiscussion")
     */
    public function download_discussion(Request $request)
    {
        return $this->render("{$this->checkUserRole()}add.html.twig", $this->chatManager->prepareSideBar($request, $this->current_logged_user));
    }

    private function checkUserRole()
    {
        $path = "chat/";

        if($this->getUser()->hasRole('ROLE_ADMIN')) {
            $path .= "admin/";
        } else {
            $path .= "user/";
        }

        return $path;
    }
}
