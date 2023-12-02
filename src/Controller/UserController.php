<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\ElementRepository;
use App\Repository\MineralRepository;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\LivingThingRepository;
use App\Repository\NotificationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user", name="user")
 */
class UserController extends AbstractController
{
    private User $user;
    private ArticleRepository $articleRepository;
    private ElementRepository $elementRepository;
    private MineralRepository $mineralRepository;
    private LivingThingRepository $livingThingRepository;
    private NotificationRepository $notificationRepository;

    public function __construct(
        Security $security,
        ArticleRepository $articleRepository,
        ElementRepository $elementRepository,
        MineralRepository $mineralRepository,
        LivingThingRepository $livingThingRepository,
        NotificationRepository $notificationRepository
    ) {
        $this->user = $security->getUser();
        $this->articleRepository = $articleRepository;
        $this->elementRepository = $elementRepository;
        $this->mineralRepository = $mineralRepository;
        $this->livingThingRepository = $livingThingRepository;
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @Route("/", name="Home")
     */
    public function user_home()
    {
        $offset = 1;
        $limit = 4;
        
        return $this->render('user/home/index.html.twig', [
            "nbrLivingBeing" => $this->livingThingRepository->countLivingThings(),
            "nbrElement" => $this->elementRepository->countElements(),
            "nbrMineral" => $this->mineralRepository->countMinerals(),
            "recent_posts" => $this->articleRepository->getArticlesApproved($offset, $limit),
            "notifications" => $this->notificationRepository->getLatestNotifications($this->user->getId(), $offset, $limit),
            "recent_conversation" => [],
        ]);
    }
}
