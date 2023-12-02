<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Repository\NotificationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/user", name="user")
 */
class NotificationController extends AbstractController
{
    private User $user;
    private NotificationRepository $notificationRepository;

    public function __construct(
        Security $security,
        NotificationRepository $notificationRepository
    ) {
        $this->user = $security->getUser();
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @Route("/notifications", name="Notifs")
     */
    public function user_notifications(Request $request) : Response
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $notifications = $this->notificationRepository->getLatestNotifications($this->user->getId(), $offset, $limit);
        $totalPage = ceil($this->notificationRepository->countNotification($this->user->getId()) / $limit);

        return $this->render('user/notifications/index.html.twig', [
            "notifications" => $notifications,
            "offset" => $offset,
            "nbrPage" => $totalPage,
        ]);
    }
}
