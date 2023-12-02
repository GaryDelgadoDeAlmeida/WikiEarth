<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Entity\LivingThing;
use App\Form\LivingThingType;
use App\Manager\ContactManager;
use App\Entity\ArticleLivingThing;
use App\Manager\StatisticsManager;
use App\Manager\LivingThingManager;
use App\Form\ArticleLivingThingType;
use App\Manager\MediaGalleryManager;
use App\Manager\NotificationManager;
use App\Repository\ArticleRepository;
use App\Repository\LivingThingRepository;
use App\Manager\ArticleLivingThingManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/user", name="user")
 */
class LivingThingController extends AbstractController
{
    private User $user;
    private EntityManagerInterface $em;
    
    private ContactManager $contactManager;
    private StatisticsManager $statisticManager;
    private LivingThingManager $livingThingManager;
    private MediaGalleryManager $mediaGalleryManager;
    private NotificationManager $notificationManager;
    private ArticleLivingThingManager $articleLivingThingManager;
    
    private ArticleRepository $articleRepository;
    private LivingThingRepository $livingThingRepository;

    public function __construct(
        Security $security,
        ContactManager $contactManager,
        StatisticsManager $statisticManager,
        LivingThingManager $livingThingManager,
        MediaGalleryManager $mediaGalleryManager,
        NotificationManager $notificationManager,
        ArticleLivingThingManager $articleLivingThingManager,
        ArticleRepository $articleRepository,
        LivingThingRepository $livingThingRepository
    ) {
        $this->user = $security->getUser();
        
        $this->contactManager = $contactManager;
        $this->statisticManager = $statisticManager;
        $this->livingThingManager = $livingThingManager;
        $this->mediaGalleryManager = $mediaGalleryManager;
        $this->notificationManager = $notificationManager;
        $this->articleLivingThingManager = $articleLivingThingManager;
        
        $this->articleRepository = $articleRepository;
        $this->livingThingRepository = $livingThingRepository;
    }

    /**
     * @Route("/living-thing", name="LivingThing")
     */
    public function user_living_thing(Request $request) : Response
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $search = !empty($request->get("search")) ? $request->get("search") : null;
        $filterBy = !empty($request->get("filter-by-livingThing")) ? $request->get("filter-by-livingThing") : "all";
        $categoryBy = !empty($request->get("category-by-livingThing")) ? $request->get("category-by-livingThing") : "all";
        $categoryChoices = [
            "all" => "All",
            "animalia" => "Animalia",
            "plantae" => "Plantae",
            "fungi" => "Fungi",
            "bacteria" => "Bacteria",
            "archaea" => "Archaea",
            "protozoa" => "Protozoa",
            "chromista" => "Chromista",
        ];
        $filterChocies = [
            "all" => "All",
            "have-article" => "Have an article",
            "not-have-article" => "Not have an article"
        ];
        $livingThing = [];
        $nbrPages = 1;
        
        if(empty($search)) {
            if($filterBy != "all" && array_key_exists($filterBy, $filterChocies)) {
                $request->attributes->set("category-by-livingThing", "all");

                if($filterBy == "have-article") {
                    $livingThing = $this->livingThingRepository->getLivingThingWithArticle($offset, $limit);
                    $nbrPages = $this->livingThingRepository->countLivingThingWithArticle();
                } else {
                    $livingThing = $this->livingThingRepository->getLivingThingWithoutArticle($offset, $limit);
                    $nbrPages = $this->livingThingRepository->countLivingThingWithoutArticle();
                }
            } elseif($categoryBy != "all" && array_key_exists($categoryBy, $categoryChoices)) {
                $request->attributes->set("filter-by-livingThing", "all");

                $livingThing = $this->livingThingRepository->getLivingThingKingdom(\ucfirst($categoryBy), $offset, $limit);
                $nbrPages = $this->livingThingRepository->countLivingThingKingdom(\ucfirst($categoryBy));
            } else {
                $livingThing = $this->livingThingRepository->getLivingThings($offset, $limit);
                $nbrPages = ceil($this->livingThingRepository->countLivingThings() / $limit);
            }
        } else {
            $categoryBy = "all";
            $filterBy = "all";
            $livingThing = $this->livingThingRepository->searchLivingThing($search, $offset, $limit);
            $nbrPages = ceil($this->livingThingRepository->countSearchLivingThing($search) / $limit);
        }

        return $this->render('user/article/living-things/listLivingThing.html.twig', [
            "livingThings" => $livingThing,
            "search" => $search,
            "offset" => $offset,
            "total_page" => $nbrPages,
            "category_by" => $categoryBy,
            "filter_by" => $filterBy,
            "categoryChoices" => $categoryChoices,
            "filterChoices" => $filterChocies
        ]);
    }

    /**
     * @Route("/living-thing/add", name="AddLivingThing")
     */
    public function user_add_living_thing(Request $request)
    {
        $formLivingThing = $this->createForm(LivingThingType::class, $livingThing = new LivingThing());
        $formLivingThing->handleRequest($request);
        $message = [];

        if($formLivingThing->isSubmitted() && $formLivingThing->isValid()) {
            if(empty($this->livingThingRepository->getLivingThingByName($livingThing->getName()))) {
                $message = $this->livingThingManager->setLivingThing(
                    $formLivingThing["imgPath"]->getData(), 
                    $livingThing
                );

                if(empty($message) || $message["error"] == false) {
                    // We send a notification to the user
                    $this->notificationManager->userCreateArticle($this->user);

                    // Send a notification email to admin
                    $this->contactManager->sendEmailToAdmin($this->getParameter("admin_email"), "New article {$livingThing->getName()}", "A new article has been created. Please, go to the back office to approuve or delete the article.");
                    
                    // Article creation statistics
                    $this->statisticsManager->updateArticleCreationsStatistics();
                }
            } else {
                // On envoi une notif à l'utilisateur l'avertissant que le living thing qu'il a tenté d'ajouté existe déjà
                $this->notificationManager->livingThingAlreadyExist($this->user);
                return $this->redirectToRoute("userLivingThing");
            }
        }

        return $this->render('user/article/living-things/formLivingThing.html.twig', [
            "formLivingThing" => $formLivingThing->createView(),
            "response" => $message
        ]);
    }

    /**
     * @Route("/living-thing/{id}/article", name="LivingThingCreateArticle")
     */
    public function user_living_thing_create_article($id, Request $request)
    {
        $articleLivingThing = $this->articleRepository->getArticleByLivingThing($id);
        $message = [];

        if(empty($articleLivingThing)) {
            $livingThing = $this->livingThingRepository->getLivingThing($id);

            if(!empty($livingThing)) {
                $formArticle = $this->createForm(ArticleLivingThingType::class, $articleLivingThing = new ArticleLivingThing());
                $formArticle->get('livingThing')->setData($livingThing);
                $formArticle->handleRequest($request);

                if($formArticle->isSubmitted() && $formArticle->isValid()) {
                    $existingLivingThing = $this->livingThingRepository->getLivingThingByName($livingThing->getName());
                    if(!empty($existingLivingThing)) {
                        // On effectue en premier le traitement sur le living thing
                        $message = $this->livingThingManager->setLivingThing(
                            $formArticle["livingThing"]["imgPath"]->getData(),
                            $livingThing
                        );
                    } else {
                        $livingThing = $existingLivingThing;
                    }

                    if(empty($message) || $message['error'] == false) {
                        if(is_null($livingThing->getArticleLivingThing())) {
                            // On traite maintenant l'article (pour cause ces liaisons avec les autres tables)
                            $message = $this->articleLivingThingManager->setArticleLivingThing(
                                $articleLivingThing,
                                $livingThing,
                                $this->user
                            );

                            // Une fois le traitement du living thing et de l'article, on traite les médias (qui seront liée à l'article)
                            $this->mediaGalleryManager->setMediaGalleryLivingThing(
                                $formArticle["mediaGallery"]->getData(),
                                $articleLivingThing
                            );
                        } else {
                            $message = [
                                "error" => true,
                                "class" => "danger",
                                "message" => "L'être vivant {$livingThing->getName()} possède déjà un article. L'ajout de ce nouvel article est annulé."
                            ];
                        }
                    }

                    // We send a notification to the user
                    $this->notificationManager->userCreateArticle($this->user);
                    
                    // Send a notification email to admin
                    $this->contactManager->sendEmailToAdmin($this->getParameter("admin_email"), "New article {$livingThing->getName()}", "A new article has been created. Please, go to the back office to approuve or delete the article.");
                    
                    // Article creation statistics
                    $this->statisticsManager->updateArticleCreationsStatistics();
                    
                    return $this->redirectToRoute("userLivingThing");
                }
            } else {
                // On envoi une notif à l'utilisateur l'avertissant que le living thing qu'il a tenté d'ajouté n'existe pas
                $this->notificationManager->livingThingNotFound($this->user);

                return $this->redirectToRoute("userLivingThing", [
                    "class" => "danger",
                    "message" => "This living thing does not exist."
                ], 307);
            }
        } else {
            // On envoi une notif à l'utilisateur l'avertissant que le living thing possède déjà un article
            $this->notificationManager->articleAlreadyExist($livingThing->getName(), $this->user);

            return $this->redirectToRoute("userLivingThing", [
                "class" => "danger",
                "message" => "This living thing, {$livingThing->getName()}, already have an article."
            ], 307);
        }

        return $this->render('user/article/living-things/formArticle.html.twig', [
            "formArticle" => $formArticle->createView(),
            "response" => $message
        ]);
    }

    /**
     * @Route("/living-thing/{id}/edit", name="EditLivingThing")
     */
    public function user_edit_living_thing(LivingThing $livingThing, Request $request)
    {
        $formLivingThing = $this->createForm(LivingThingType::class, $livingThing);
        $formLivingThing->handleRequest($request);
        $response = [];

        if($formLivingThing->isSubmitted() && $formLivingThing->isValid()) {

            // On effectue en premier le traitement sur le living thing
            $response = $this->livingThingManager->setLivingThing(
                $formLivingThing["imgPath"]->getData(), 
                $livingThing
            );
        }

        return $this->render('user/article/living-things/formLivingThing.html.twig', [
            "formLivingThing" => $formLivingThing->createView(),
            "response" => $response
        ]);
    }
}
