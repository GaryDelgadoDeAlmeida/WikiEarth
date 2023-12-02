<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\ArticleRepository;
use App\Repository\ElementRepository;
use App\Repository\MineralRepository;
use App\Repository\StatisticsRepository;
use App\Repository\LivingThingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin", name="admin")
 */
class AdminController extends AbstractController
{
    private User $user;
    private UserRepository $userRepository;
    private ArticleRepository $articleRepository;
    private MineralRepository $mineralRepository;
    private ElementRepository $elementRepository;
    private StatisticsRepository $statisticsRepository;
    private LivingThingRepository $livingThingRepository;

    public function __construct(
        Security $security,
        UserRepository $userRepository,
        ArticleRepository $articleRepository,
        MineralRepository $mineralRepository,
        ElementRepository $elementRepository,
        StatisticsRepository $statisticsRepository,
        LivingThingRepository $livingThingRepository
    ) {
        $this->user = $security->getUser();
        $this->userRepository = $userRepository;
        $this->articleRepository = $articleRepository;
        $this->mineralRepository = $mineralRepository;
        $this->elementRepository = $elementRepository;
        $this->statisticsRepository = $statisticsRepository;
        $this->livingThingRepository = $livingThingRepository;
    }
    
    /**
     * @Route("/", name="Home")
     */
    public function admin_home()
    {
        $past_month_date = $current_date = new \DateTimeImmutable();
        $past_month_date = $past_month_date->modify("-6 month");
        $latestStatistics = $this->statisticsRepository->getStatisticsByDateInterval($current_date, $past_month_date);

        // We only get the user connection part
        $latestUsersActivities = [];
        foreach($latestStatistics as $oneLatestStatistic) {
            $latestUsersActivities[] = [
                "label" => $oneLatestStatistic->getCreatedAt()->format("M"),
                "y" => $oneLatestStatistic->getNbrUsersConnection()
            ];
        }

        return $this->render('admin/home/index.html.twig', [
            "nbrUsers" => $this->userRepository->countUsers($this->user->getId()),
            "nbrArticles" => $this->articleRepository->countArticlesApproved(),
            "nbrLivingThings" => $this->livingThingRepository->countLivingThings(),
            "nbrElements" => $this->elementRepository->countElements(),
            "nbrMinerals" => $this->mineralRepository->countMinerals(),
            "nbrChimicalReaction" => 0,
            "articles" => $this->articleRepository->getArticles(1, 5),
            "dataPoints" => $latestUsersActivities,
        ]);
    }
}
