<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Manager\ArticleManager;
use App\Manager\ContactManager;
use App\Manager\ElementManager;
use App\Manager\MineralManager;
use App\Form\ArticleElementType;
use App\Form\ArticleMineralType;
use App\Entity\ArticleLivingThing;
use App\Manager\StatisticsManager;
use App\Manager\LivingThingManager;
use App\Form\ArticleLivingThingType;
use App\Manager\MediaGalleryManager;
use App\Manager\NotificationManager;
use App\Repository\ArticleRepository;
use App\Manager\ArticleMineralManager;
use App\Manager\ArticleLivingThingManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/user", name="user")
 */
class ArticleController extends AbstractController
{
    private User $user;
    
    private ArticleManager $articleManager;
    private ContactManager $contactManager;
    private MineralManager $mineralManager;
    private ElementManager $elementManager;
    private StatisticsManager $statisticsManager;
    private LivingThingManager $livingThingManager;
    private MediaGalleryManager $mediaGalleryManager;
    private NotificationManager $notificationManager;
    private ArticleMineralManager $articleMineralManager;
    private ArticleLivingThingManager $articleLivingThingManager;
    
    private ArticleRepository $articleRepository;
    
    public function __construct(
        Security $security,
        ArticleManager $articleManager,
        ContactManager $contactManager,
        MineralManager $mineralManager,
        ElementManager $elementManager,
        StatisticsManager $statisticsManager,
        LivingThingManager $livingThingManager,
        MediaGalleryManager $mediaGalleryManager,
        NotificationManager $notificationManager,
        ArticleMineralManager $articleMineralManager,
        ArticleLivingThingManager $articleLivingThingManager,
        ArticleRepository $articleRepository
    ) {
        $this->user = $security->getUser();
        $this->articleManager = $articleManager;
        $this->contactManager = $contactManager;
        $this->mineralManager = $mineralManager;
        $this->elementManager = $elementManager;
        $this->statisticsManager = $statisticsManager;
        $this->livingThingManager = $livingThingManager;
        $this->mediaGalleryManager = $mediaGalleryManager;
        $this->notificationManager = $notificationManager;
        $this->articleMineralManager = $articleMineralManager;
        $this->articleLivingThingManager = $articleLivingThingManager;
        $this->articleRepository = $articleRepository;
    }

    /**
     * @Route("/article", name="Article")
     */
    public function user_article(Request $request) : Response
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $search = !empty($request->get('offset')) ? $request->get('offset') : null;
        $category_by = !empty($request->get('category-by-livingThing')) ? $request->get('category-by-livingThing') : "all";
        $categoryChoices = [
            "all" => "All",
            "animalia" => "Animals",
            "plantae" => "Plants",
            "insecta" => "Insecta",
            "bacteria" => "Bacteria",
            "virus" => "Virus"
        ];
        $nbrPages = null;

        if(!empty($search)) {
            $category_by = "all";
            $articleLivingThing = $this->articleRepository->searchArticleLivingThings($search);
            $nbrPages = ceil($this->articleRepository->countSearchArticleLivingThings() / $limit);
        } else {
            if($category_by == "all") {
                $articleLivingThing = $this->articleRepository->getArticleLivingThingsApproved($offset, $limit);
                $nbrPages = ceil($this->articleRepository->countArticleLivingThingsApproved() / $limit);
            } else {
                $articleLivingThing = $this->articleRepository->getArticleLivingThingsByLivingThingKingdom($category_by, $offset, $limit);
                $nbrPages = ceil($this->articleRepository->countArticleLivingThingsByKingdom($category_by, $limit));
            }
        }

        return $this->render('user/article/living-things/listArticle.html.twig', [
            "articles" => $articleLivingThing,
            "search" => $search,
            "category_by" => $category_by,
            "categoryChoices" => $categoryChoices,
            "offset" => $offset,
            "total_page" => $nbrPages
        ]);
    }

    /**
     * @Route("/article/add", name="AddArticle")
     */
    public function user_add_article(Request $request) : Response
    {
        $formArticle = $this->createForm(ArticleLivingThingType::class, $article = new ArticleLivingThing());
        $formArticle->handleRequest($request);
        $response = [];

        if($formArticle->isSubmitted() && $formArticle->isValid()) {
            $livingThing = $formArticle["livingThing"]->getData();
            
            // On effectue en premier le traitement sur le living thing
            $response = $this->livingThingManager->setLivingThing(
                $formArticle["livingThing"]['imgPath']->getData(),
                $livingThing,
                $this->manager
            );

            // On traite maintenant l'article (pour cause ces liaisons avec les autres tables)
            $response = $this->articleLivingThingManager->setArticleLivingThing(
                $article, 
                $livingThing,
                $this->manager, 
                $this->user
            );

            // Une fois le traitement du living thing et de l'article, on traite les médias (qui seront liée à l'article)
            $response = $this->mediaGalleryManager->setMediaGalleryLivingThing(
                $formArticle["mediaGallery"]->getData(),
                $articleLivingThing,
                $this->manager
            );

            // TODO : vérifier le bon fonctionnement de cette méthode
            // $this->referenceManager->setReferences(
            //     $formArticle["references"]->getData(),
            //     $articleLivingThing,
            //     $this->manager
            // );

            // We send a notification to the user
            $this->notificationManager->userCreateArticle($this->user);

            // Send a notification email to admin
            $this->contactManager->sendEmailToAdmin($this->getParameter("admin_email"), "New article {$livingThing->getName()}", "A new article has been created. Please, go to the back office to approuve or delete the article.");

            // Article creation statistics
            $this->statisticsManager->updateArticleCreationsStatistics();
        }

        return $this->render('user/article/living-things/formArticle.html.twig', [
            "formArticle" => $formArticle->createView(),
            "response" => $response
        ]);
    }

    /**
     * @Route("/article/{category}/{id}/edit", name="EditArticle")
     */
    public function user_edit_article(string $category, int $id, Request $request) : Response
    {
        $response = [];

        if($category == "living-thing") {
            $article = $this->articleRepository->getArticleByLivingThing($id);
            
            if(!empty($article)) {
                $formArticle = $this->createForm(ArticleLivingThingType::class, $articleLivingThing = $article->getArticleLivingThing());
                $formArticle->get('livingThing')->setData($article->getIdLivingThing());
                $formArticle->handleRequest($request);

                if($formArticle->isSubmitted() && $formArticle->isValid()) {

                    $livingThing = $formArticle["livingThing"]->getData();
                    
                    // On effectue en premier le traitement sur le living thing
                    $this->livingThingManager->setLivingThing(
                        $formArticle["livingThing"]["imgPath"]->getData(),
                        $livingThing,
                        $this->manager
                    );

                    // On traite maintenant l'article (pour cause ces liaisons avec les autres tables)
                    $this->articleLivingThingManager->setArticleLivingThing(
                        $articleLivingThing, 
                        $livingThing,
                        $this->manager, 
                        $this->user
                    );

                    // Une fois le traitement du living thing et de l'article, on traite les médias (qui seront liée à l'article)
                    $this->mediaGalleryManager->setMediaGalleryLivingThing(
                        $formArticle["mediaGallery"]->getData(),
                        $articleLivingThing,
                        $this->manager
                    );

                    // TODO : vérifier le bon fonctionnement de cette méthode
                    // $this->referenceManager->setReferences(
                    //     $formArticle["references"]->getData(),
                    //     $articleLivingThing,
                    //     $this->manager
                    // );

                    // We send a notification to the user
                    $this->notificationManager->userUpdateArticle($this->user);
                }

                return $this->render('user/article/living-things/formArticle.html.twig', [
                    "formArticle" => $formArticle->createView(),
                    "response" => $response
                ]);
            } else {
                return $this->redirectToRoute("userLivingThing", [
                    "class" => "danger",
                    "message" => "This living thing does not exist."
                ], 307);
            }
        } elseif($category == "element") {
            $article = $this->articleRepository->getArticleByElement($id);
            
            if(!empty($article)) {
                $formElement = $this->createForm(ArticleElementType::class, $articleElement = $article->getArticleElement());
                $formElement->get("element")->setData($articleElement->getElement());
                $formElement->handleRequest($request);

                if($formElement->isSubmitted() && $formElement->isValid()) {
                    $response = $this->elementManager->setElement(
                        $formElement["imgPath"]->getData(), 
                        $articleElement,
                        $formElement,
                        $this->manager
                    );
                }

                return $this->render('user/article/element/formArticle.html.twig', [
                    "formArticle" => $formElement->createView(),
                    "response" => $response
                ]);
            }
        } elseif($category == "mineral") {
            $article = $this->articleRepository->getArticleByMineral($id);
            
            if(!empty($article)) {
                $articleMineral = $article->getArticleMineral();
                $mineral = $articleMineral->getMineral();
                $formMineral = $this->createForm(ArticleMineralType::class, $articleMineral);
                $formMineral->get("mineral")->setData($mineral);
                $formMineral->get("mineral")->get('imaStatus')->setData(implode(", ", $mineral->getImaStatus()));
                $formMineral->handleRequest($request);

                if($formMineral->isSubmitted() && $formMineral->isValid()) {

                    // Update minéral
                    $response = $this->mineralManager->setMineral(
                        $formMineral["mineral"]["imgPath"]->getData(), 
                        $mineral,
                        $formMineral["mineral"],
                        $this->manager
                    );

                    // Update the content of the article
                    $response = $this->articleMineralManager->setArticleMineral(
                        $articleMineral,
                        $mineral,
                        $this->manager
                    );

                    // Update Reference
                    $response = $this->referenceManager;

                    // Update Media
                    $response = $this->mediaGalleryManager;

                    // Update of the article
                    $response = $this->articleManager->setArticle(
                        $articleMineral,
                        $this->manager,
                        $this->user
                    );
                }

                return $this->render('user/article/minerals/formArticle.html.twig', [
                    "formArticle" => $formMineral->createView(),
                    "response" => $response
                ]);
            }
        }

        throw new \Exception("This category {$category} isn't allowed.");
    }
}
