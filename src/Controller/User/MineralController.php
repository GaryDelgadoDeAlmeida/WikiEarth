<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Entity\Mineral;
use App\Form\MineralType;
use App\Entity\ArticleMineral;
use App\Manager\ArticleManager;
use App\Manager\ContactManager;
use App\Manager\MineralManager;
use App\Form\ArticleMineralType;
use App\Manager\StatisticsManager;
use App\Manager\MediaGalleryManager;
use App\Manager\NotificationManager;
use App\Repository\MineralRepository;
use App\Manager\ArticleMineralManager;
use App\Repository\ArticleMineralRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/user", name="user")
 */
class MineralController extends AbstractController
{
    private User $user;
    private EntityManagerInterface $em;

    private ArticleManager $articleManager;
    private MineralManager $mineralManager;
    private ContactManager $contactManager;
    private StatisticsManager $statisticsManager;
    private NotificationManager $notificationManager;
    private MediaGalleryManager $mediaGalleryManager;
    private ArticleMineralManager $articleMineralManager;

    private MineralRepository $mineralRepository;
    private ArticleMineralRepository $articleMineralRepository;

    public function __construct(
        Security $security,
        ArticleManager $articleManager,
        MineralManager $mineralManager,
        ContactManager $contactManager,
        StatisticsManager $statisticsManager,
        NotificationManager $notificationManager,
        MediaGalleryManager $mediaGalleryManager,
        ArticleMineralManager $articleMineralManager,
        MineralRepository $mineralRepository,
        ArticleMineralRepository $articleMineralRepository
    ) {
        $this->user = $security->getUser();
        $this->articleManager = $articleManager;
        $this->mineralManager = $mineralManager;
        $this->contactManager = $contactManager;
        $this->statisticsManager = $statisticsManager;
        $this->notificationManager = $notificationManager;
        $this->mediaGalleryManager = $mediaGalleryManager;
        $this->articleMineralManager = $articleMineralManager;
        $this->mineralRepository = $mineralRepository;
        $this->articleMineralRepository = $articleMineralRepository;
    }
    
    /**
     * @Route("/mineral", name="Mineral")
     */
    public function user_mineral(Request $request)
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? intval($request->get('offset')) : 1;
        $search = !empty($request->get("search")) ? $request->get("search") : null;
        $filterBy = !empty($request->get("filter-by-mineral")) ? $request->get("filter-by-mineral") : "all";
        $filterChocies = [
            "all" => "All",
            "have-article" => "Have an article",
            "not-have-article" => "Not have an article"
        ];
        $minerals = [];
        $nbrPages = 1;
        
        if(empty($search)) {
            if($filterBy !== "all" && array_key_exists($filterBy, $filterChocies)) {
                if($filterBy == "have-article") {
                    $minerals = $this->mineralRepository->getMineralsWithArticle($offset, $limit);
                    $nbrPages = ceil($this->mineralRepository->countMineralsWithArticle() / $limit);
                } elseif($filterBy == "not-have-article") {
                    $minerals = $this->mineralRepository->getMineralsWithoutArticle($offset, $limit);
                    $nbrPages = ceil($this->mineralRepository->countMineralsWithoutArticle() / $limit);
                }
            } else {
                $minerals = $this->mineralRepository->getMinerals($offset, $limit);
                $nbrPages = ceil($this->mineralRepository->countMinerals() / $limit);
            }
        } else {
            $filterBy = "all";
            $minerals = $this->mineralRepository->searchMineral($search, $offset, $limit);
            $nbrPages = ceil($this->mineralRepository->countSearchMineral($search) / $limit);
        }

        return $this->render('user/article/minerals/listMineral.html.twig', [
            "minerals" => $minerals,
            "search" => $search,
            "offset" => $offset,
            "total_page" => $nbrPages,
            "filter_by" => $filterBy,
            "filterChoices" => $filterChocies
        ]);
    }

    /**
     * @Route("/mineral/add", name="AddMineral")
     */
    public function user_add_mineral(Request $request)
    {
        $formMineral = $this->createForm(MineralType::class, $mineral = new Mineral());
        $formMineral->handleRequest($request);
        $response = [];

        if($formMineral->isSubmitted() && $formMineral->isValid()) {
            if(empty($this->mineralRepository->getMineralByName($mineral->getName()))) {
                $mineral->setImaStatus(explode(", ", $formMineral['imaStatus']->getData()));

                $response = $this->mineralManager->setMineral(
                    $formMineral["imgPath"]->getData(), 
                    $mineral, 
                    $formMineral
                );

                // We send a notification to the user
                $this->notificationManager->userCreateArticle($this->user);

                // Send a notification email to admin
                $this->contactManager->sendEmailToAdmin($this->getParameter("admin_email"), "New article {$mineral->getName()}", "A new article has been created. Please, go to the back office to approuve or delete the article.");
                
                // Article creation statistics
                $this->statisticsManager->updateArticleCreationsStatistics();
                
                return $this->redirectToRoute("userMineral");
            } else {
                // On envoi une notif à l'utilisateur l'avertissant que le living thing qu'il a tenté d'ajouté existe déjà
                $this->notificationManager->mineralAlreadyExist($this->user);
                return $this->redirectToRoute("userMineral");
            }
        }

        return $this->render('user/article/minerals/formMineral.html.twig', [
            "formMineral" => $formMineral->createView(),
            "response" => $response
        ]);
    }

    /**
     * @Route("/mineral/{id}/article", name="MineralCreateArticle")
     */
    public function user_mineral_create_article($id, Request $request)
    {
        $articleMineral = $this->articleMineralRepository->findOneBy(["mineral" => $id]);

        if(empty($articleMineral)) {
            $mineral = $this->mineralRepository->find($id);

            if(!empty($mineral)) {
                $formArticle = $this->createForm(ArticleMineralType::class, $articleMineral = new ArticleMineral());
                $formArticle->get('mineral')->setData($mineral);
                $formArticle->get('mineral')->get('imaStatus')->setData(implode(", ", $mineral->getImaStatus()));
                $formArticle->handleRequest($request);
                $response = [];

                if($formArticle->isSubmitted() && $formArticle->isValid()) {

                    $mineral->setImaStatus(explode(", ", $formArticle["mineral"]['imaStatus']->getData()));

                    // On effectue en premier le traitement sur le living thing
                    $response = $this->mineralManager->setMineral(
                        $formArticle["mineral"]["imgPath"]->getData(),
                        $mineral,
                        $formArticle["mineral"]
                    );

                    // On traite maintenant l'articleMineral (pour cause ces liaisons avec les autres tables)
                    $response = $this->articleMineralManager->setArticleMineral(
                        $articleMineral,
                        $mineral
                    );

                    // On traite maintenant l'article (pour cause ces liaisons avec les autres tables)
                    $response = $this->articleManager->insertArticle(
                        $articleMineral,
                        $this->user
                    );

                    // Une fois le traitement du living thing et de l'article, on traite les médias (qui seront liée à l'article)
                    $response = $this->mediaGalleryManager->setMediaGalleryMinerals(
                        $formArticle["mediaGallery"]->getData(),
                        $articleMineral
                    );

                    // We send a notification to the user
                    $this->notificationManager->userCreateArticle($this->user);

                    // Send a notification email to admin
                    $this->contactManager->sendEmailToAdmin($this->getParameter("admin_email"), "New article {$mineral->getName()}", "A new article has been created. Please, go to the back office to approuve or delete the article.");

                    // Article creation statistics
                    $this->statisticsManager->updateArticleCreationsStatistics();

                    return $this->redirectToRoute("userMineral");
                }

                return $this->render('user/article/minerals/formArticle.html.twig', [
                    "formArticle" => $formArticle->createView(),
                    "response" => $response
                ]);
            } else {
                // On envoi une notif à l'utilisateur l'avertissant que le mineral qu'il a tenté d'ajouté n'existe pas
                $this->notificationManager->mineralNotFound($this->user);
                return $this->redirectToRoute("userMineral", [
                    "class" => "danger",
                    "message" => "This mineral does not exist."
                ], 307);
            }
        } else {
            // On envoi une notif à l'utilisateur l'avertissant que le mineral possède déjà un article
            $this->notificationManager->articleAlreadyExist($articleMineral->getMineral()->getName(), $this->user);

            return $this->redirectToRoute("userMineral", [
                "class" => "danger",
                "message" => "This mineral already have an article."
            ], 307);
        }
    }
}
