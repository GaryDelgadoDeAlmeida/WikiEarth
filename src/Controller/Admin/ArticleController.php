<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\ArticleElement;
use App\Entity\ArticleMineral;
use App\Manager\ElementManager;
use App\Manager\MineralManager;
use App\Form\ArticleElementType;
use App\Form\ArticleMineralType;
use App\Entity\ArticleLivingThing;
use App\Manager\LivingThingManager;
use App\Form\ArticleLivingThingType;
use App\Manager\NotificationManager;
use App\Repository\ArticleRepository;
use App\Repository\ElementRepository;
use App\Repository\MineralRepository;
use App\Manager\ArticleElementManager;
use App\Manager\ArticleMineralManager;
use App\Manager\ArticleLivingThingManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin", name="admin")
 */
class ArticleController extends AbstractController
{
    private User $user;

    private ElementManager $elementManager;
    private MineralManager $mineralManager;
    private LivingThingManager $livingThingManager;
    private NotificationManager $notificationManager;
    private ArticleElementManager $articleElementManager;
    private ArticleMineralManager $articleMineralManager;
    private ArticleLivingThingManager $articleLivingThingManager;

    private ArticleRepository $articleRepository;
    private ElementRepository $elementRepository;
    private MineralRepository $mineralRepository;

    function __construct(
        Security $security,
        ElementManager $elementManager,
        MineralManager $mineralManager,
        LivingThingManager $livingThingManager,
        NotificationManager $notificationManager,
        ArticleElementManager $articleElementManager,
        ArticleMineralManager $articleMineralManager,
        ArticleLivingThingManager $articleLivingThingManager,
        ArticleRepository $articleRepository,
        ElementRepository $elementRepository,
        MineralRepository $mineralRepository
    ) {
        $this->user = $security->getUser();
        $this->elementManager = $elementManager;
        $this->mineralManager = $mineralManager;
        $this->livingThingManager = $livingThingManager;
        $this->notificationManager = $notificationManager;
        $this->articleElementManager = $articleElementManager;
        $this->articleMineralManager = $articleMineralManager;
        $this->articleLivingThingManager = $articleLivingThingManager;
        $this->articleRepository = $articleRepository;
        $this->elementRepository = $elementRepository;
        $this->mineralRepository = $mineralRepository;
    }

    /**
     * @Route("/article", name="Article")
     */
    public function admin_article(Request $request) : Response
    {
        return $this->render('admin/article/index.html.twig');
    }

    /**
     * Affiche les articles selon la categorie d'appartenance. C'est-à-dire, on affiche 
     * les articles sur les êtres vivants si c'est la categorie demandée est les êtres vivants
     * 
     * @Route("/article/{category}", name="ArticleByCategory")
     */
    public function admin_article_by_category(string $category, Request $request) : Response
    {
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $search = !empty($request->get('search')) ? $request->get('search') : null;
        $filterBy = !empty($request->get('filterBy')) ? $request->get('filterBy') : "all";
        $filterByChoices = ["all" => "All", "approved-article" => "Approved", "not-approuved-article" => "To Approuve"];
        $response = !empty($request->get('response')) ? $request->get('response') : [];
        $limit = 10;
        $nbrOffset = 1;

        if($category == "living-thing") {
            $nbrLivingThing = $this->articleRepository->countArticleLivingThings();
            $nbrOffset = $nbrLivingThing > $limit ? ceil($nbrLivingThing / $limit) : $nbrOffset;

            return $this->render('admin/article/living-thing/listArticle.html.twig', [
                "articles" => $this->articleRepository->getArticleLivingThings($offset, $limit),
                "nbrOffset" => $nbrOffset,
                "offset" => $offset,
                "category" => $category,
                "response" => $response
            ]);
        } elseif($category == "natural-elements") {
            $articleElements = $this->articleRepository->getArticleElements($offset, $limit);
            $nbrElements = $this->articleRepository->countArticleElements();
            $nbrOffset = $nbrElements > $limit ? ceil($nbrElements / $limit) : $nbrOffset;

            return $this->render('admin/article/natural-elements/listArticle.html.twig', [
                "articles" => $articleElements,
                "nbrOffset" => $nbrOffset,
                "offset" => $offset,
                "category" => $category,
                "response" => $response
            ]);
        } elseif($category == "minerals") {
            $articleMinerals = $this->articleRepository->getArticleMinerals($offset, $limit);
            $nbrMinerals = $this->articleRepository->countArticleMinerals();
            $nbrOffset = $nbrMinerals > $limit ? ceil($nbrMinerals / $limit) : $nbrOffset;

            return $this->render('admin/article/minerals/listArticle.html.twig', [
                "articles" => $articleMinerals,
                "nbrOffset" => $nbrOffset,
                "offset" => $offset,
                "category" => $category,
                "response" => $response
            ]);
        }

        return $this->redirectToRoute("adminArticle", [
            "response" => [
                "class" => "danger",
                "message" => "The category {$category} isn't allowed."
            ]
        ], 307);
    }

    /**
     * Ajout un article selon le type (la categorie => "living-thing" ou "natural-elements") de l'article.
     * 
     * @Route("/article/{category}/add", name="AddArticleByCategory")
     */
    public function admin_add_article_by_category(string $category, Request $request) : Response
    {
        $message = [];

        if($category == "living-thing") {
            $formArticle = $this->createForm(ArticleLivingThingType::class, $article = new ArticleLivingThing());
            $formArticle->handleRequest($request);

            // Quand le formulaire est soumit et valide celon la config dans l'entity
            if($formArticle->isSubmitted() && $formArticle->isValid()) {
                $livingThing = $formArticle["livingThing"]->getData();
                
                $message = $this->livingThingManager->setLivingThing(
                    $formArticle["livingThing"]["imgPath"]->getData(),
                    $livingThing
                );

                // S'il n'y a pas eu d'erreur, alors ...
                if($message["error"] == false) {
                    $message = $this->articleLivingThingManager->setArticleLivingThing(
                        $article,
                        $livingThing,
                        $this->user
                    );
                }
            }

            return $this->render('admin/article/living-thing/formArticle.html.twig', [
                "formArticle" => $formArticle->createView(),
                "category" => $category,
                "response" => $message
            ]);
        } elseif($category == "natural-elements") {
            $formArticle = $this->createForm(ArticleElementType::class, $articleElement = new ArticleElement());
            $formArticle->handleRequest($request);

            if($formArticle->isSubmitted() && $formArticle->isValid()) {
                $element = $formArticle["element"]->getData();

                $existingElement = $this->elementRepository->getElementByName($element->getScientificName());
                
                if(empty($existingElement)) {
                    $message = $this->elementManager->setElement(
                        $formArticle["element"]["imgPath"]->getData(),
                        $element,
                        $this->em
                    );
                } else {
                    $element = $existingElement;
                }

                // S'il n'y a pas eu d'erreur rencontrée avec l'insertion de l'élément naturel
                if($message["error"] == false) {
                    if(\is_null($element->getArticleElement())) {
                        $message = $this->articleElementManager->setArticleElement(
                            $articleElement,
                            $element
                        );

                        $message = $this->articleManager->insertArticle(
                            $articleElement, 
                            $this->user
                        );
                    } else {
                        $message = [
                            "error" => true,
                            "class" => "danger",
                            "message" => "L'élément {$mineral->getName()} possède déjà un article. L'ajout du nouvel article est été annulé."
                        ];
                    }
                }
            }

            return $this->render('admin/article/natural-elements/formArticle.html.twig', [
                "formArticle" => $formArticle->createView(),
                "category" => $category,
                "response" => $message
            ]);
        } elseif($category == "minerals") {
            $formArticle = $this->createForm(ArticleMineralType::class, $articleMineral = new ArticleMineral());
            $formArticle->handleRequest($request);

            if($formArticle->isSubmitted() && $formArticle->isValid()) {
                $mineral = $formArticle["mineral"]->getData();

                $existingMineral = $this->mineralRepository->getMineralByName($mineral->getName());
                if(empty($existingMineral)) {
                    $message = $this->mineralManager->setMineral(
                        $formArticle["mineral"]["imgPath"]->getData(),
                        $mineral,
                        $formArticle["mineral"]
                    );
                } else {
                    $mineral = $existingMineral;
                }

                if(empty($message) || $message["error"] == false) {
                    if(\is_null($mineral->getArticleMineral())) {
                        $message = $this->articleMineralManager->setArticleMineral(
                            $articleMineral,
                            $mineral,
                            $this->user
                        );
                    } else {
                        $message = [
                            "error" => true,
                            "class" => "danger",
                            "message" => "Le mineral {$mineral->getName()} possède déjà un article. L'ajout du nouvel article est annulé."
                        ];
                    }
                }

                if(empty($message) || $message["error"] == false) {
                    $message = $this->articleManager->insertArticle(
                        $articleMineral,
                        $this->user
                    );
                }

                if(empty($message) || $message["error"] == false) {
                    $message = $this->mediaGalleryManager->setMediaGalleryMinerals(
                        $formArticle["mediaGallery"]->getData(),
                        $articleMineral
                    );
                }
            }

            return $this->render('admin/article/minerals/formArticle.html.twig', [
                "formArticle" => $formArticle->createView(),
                "category" => $category,
                "response" => $message
            ]);
        }

        return $this->redirectToRoute("adminArticle", [
            "class" => "danger",
            "message" => "The category {$category} isn't allowed."
        ], 307);
    }

    /**
     * @Route("/article/{category}/{id}", name="SingleArticleByCategory")
     */
    public function admin_single_article_by_category(int $id, string $category) : Response
    {
        if($category == "living-thing") {
            $article = $this->articleRepository->getArticleLivingThing($id);

            if(empty($article)) {
                return $this->redirectToRoute("adminArticleByCategory", [
                    "category" => $category,
                    "class" => "danger",
                    "message" => "This article does not exist"
                ], 307);
            }

            return $this->render('admin/article/living-thing/detailArticle.html.twig', [
                "article" => $article,
                "category" => $category
            ]);
        } elseif($category == "natural-elements") {
            $article = $this->articleRepository->getArticleElement($id);

            if(empty($article)) {
                return $this->redirectToRoute("adminArticleByCategory", [
                    "category" => $category,
                    "class" => "danger",
                    "message" => "This article does not exist"
                ], 307);
            }

            return $this->render('admin/article/natural-elements/detailArticle.html.twig', [
                "article" => $article,
                "category" => $category
            ]);
        } elseif($category == "minerals") {
            $article = $this->articleRepository->getArticleMineral($id);

            if(empty($article)) {
                return $this->redirectToRoute("adminArticleByCategory", [
                    "category" => $category,
                    "class" => "danger",
                    "message" => "This article does not exist"
                ], 307);
            }

            return $this->render('admin/article/minerals/detailArticle.html.twig', [
                "article" => $article,
                "category" => $category
            ]);
        }

        return $this->redirectToRoute("adminArticle", [
            "class" => "danger",
            "message" => "The category {$category} isn't allowed."
        ], 307);
    }

    /**
     * @Route("/article/{category}/{id}/edit", name="EditArticleByCategory")
     */
    public function admin_edit_article_by_category(int $id, string $category, Request $request) : Response
    {
        $response = [];

        if($category == "living-thing") {
            $article = $this->articleRepository->findOneBy(["id" => $id]);
            
            if(empty($article)) {
                return $this->redirectToRoute("adminArticle", [
                    "class" => "danger",
                    "message" => "This article does not exist."
                ], 307);
            }
            
            $articleLivingThing = $article->getArticleLivingThing();
            $formArticle = $this->createForm(ArticleLivingThingType::class, $articleLivingThing);
            $formArticle->get('livingThing')->setData($articleLivingThing->getLivingThing());
            $formArticle->handleRequest($request);

            if($formArticle->isSubmitted() && $formArticle->isValid()) {
                $response = $this->articleLivingThingManager->setArticleLivingThing(
                    $articleLivingThing,
                    $articleLivingThing->getLivingThing(),
                    $this->em
                );
            }

            return $this->render('admin/article/living-thing/formArticle.html.twig', [
                "formArticle" => $formArticle->createView(),
                "category" => $category,
                "response" => $response
            ]);
        } elseif($category == "natural-elements") {
            $article = $this->articleRepository->find($id);

            if(empty($article)) {
                return $this->redirectToRoute("adminArticle", [
                    "class" => "danger",
                    "message" => "This article does not exist."
                ], 307);
            }
            
            $articleElement = $article->getArticleElement();
            $formArticle = $this->createForm(ArticleElementType::class, $articleElement);
            $formArticle->get('element')->setData($articleElement->getElement());
            $formArticle->handleRequest($request);

            if($formArticle->isSubmitted() && $formArticle->isValid()) {
                $response = $this->articleElementManager->setArticleElement(
                    $formArticle,
                    $formArticle->getElement(),
                    $this->em
                );

                if(!empty($response) && $response["error"] == false) {
                    $response = $this->articleManager->insertArticle(
                        $articleElement,
                        $formArticle["element"]->getData(),
                        $this->em,
                        $this->user
                    );

                    if(!empty($response) && $response["error"] == false) {
                        $response = $this->mediaGalleryManager->setMediaGalleryElements(
                            $formArticle["mediaGallery"]->getData(),
                            $articleElement,
                            $this->em
                        );
                    }
                }
            }

            return $this->render('admin/article/natural-elements/formArticle.html.twig', [
                "formArticle" => $formArticle->createView(),
                "category" => $category,
                "response" => $response
            ]);
        } elseif($category == "minerals") {
            $article = $this->articleRepository->findOneBy(["id" => $id]);

            if(empty($article)) {
                return $this->redirectToRoute("adminArticleByCategory", [
                    "category" => $category,
                    "response" => [
                        "class" => "danger",
                        "message" => "This article does not exist."
                    ]
                ], 307);
            }

            $articleMineral = $article->getArticleMineral();
            $mineral = $articleMineral->getMineral();
            $formArticle = $this->createForm(ArticleMineralType::class, $articleMineral);
            $formArticle->get('mineral')->setData($mineral);
            $formArticle->get('mineral')->get("imaStatus")->setData(implode(", ", $mineral->getImaStatus()));
            $formArticle->handleRequest($request);

            if($formArticle->isSubmitted() && $formArticle->isValid()) {
                $response = $this->mineralManager->setMineral(
                    $formArticle["mineral"]["imgPath"]->getData(),
                    $mineral,
                    $formArticle["mineral"]
                );
                
                if(!empty($response) && $response["error"] == false) {
                    $response = $this->articleMineralManager->setArticleMineral(
                        $articleMineral,
                        $formArticle["mineral"]->getData(),
                        $this->user
                    );

                    if(!empty($response) && $response["error"] == false) {
                        $response = $this->mediaGalleryManager->setMediaGalleryMinerals(
                            $formArticle["mediaGallery"]->getData(),
                            $articleMineral
                        );
                    }
                }
            }

            return $this->render('admin/article/minerals/formArticle.html.twig', [
                "formArticle" => $formArticle->createView(),
                "category" => $category,
                "response" => $response
            ]);
        }

        return $this->redirectToRoute("adminArticle", [
            "class" => "danger",
            "message" => "The category {$category} isn't allowed."
        ], 307);
    }

    /**
     * @Route("/article/{category}/{id}/approve", name="ApproveArticleByCategory")
     */
    public function admin_approve_single_article_by_category(int $id, string $category) : Response
    {
        $article = null;
        $response = [];
        if($category == "living-thing") {
            $article = $this->articleRepository->getArticleLivingThing($id);
        } elseif ($category == "natural-elements") {
            $article = $this->articleRepository->getArticleElement($id);
        } elseif($category == "minerals") {
            $article = $this->articleRepository->getArticleMineral($id);
        }

        if(empty($article)) {
            return $this->redirectToRoute("adminArticle", [
                "class" => "danger",
                "message" => "This article does not exist."
            ], 307);
        }

        if(!$article->getApproved()) {
            $this->articleRepository->save($article, true);
            $this->notificationManager->articleIsNowPublic($article);
        } else {
            $response = [
                "error" => true,
                "class" => "warning",
                "message" => "L'article {$article->getTitle()} a déjà été approuvé"
            ];
        }

        return $this->redirectToRoute("adminArticleByCategory", [
            "category" => $category
        ]);
    }

    /**
     * Possibilité d'en faire une response API
     * 
     * Supprimer un article uniquement. La liaison 1-1 avec un living thing que l'article possède
     * ne sera pas affectée
     * 
     * @Route("/article/{category}/{id}/delete", name="DeleteArticleByCategory")
     */
    public function admin_delete_article_by_category(int $id, string $category) : Response
    {
        $article = $this->articleRepository->find($id);

        if(empty($article)) {
            return $this->redirectToRoute("adminArticleByCategory", [
                "category" => $category,
                "response" => [
                    "class" => "danger",
                    "message" => "This article does not exist."
                ]
            ], 307);
        }

        if($category == "living-thing") {
            $article->getArticleLivingThing()->setIdLivingThing(null);
        } elseif($category == "natural-elements") {
            $article->getArticleElement()->setElement(null);
        } elseif($category == "minerals") {
            $article->getArticleMineral()->setMineral(null);
        }

        // Envoi d'une notification à l'utilisateur
        $this->articleRepository->remove($article, true);
        $this->notificationManager->adminRefuseArticle($article);

        return $this->redirectToRoute('adminArticleByCategory', [
            "category" => $category,
            "response" => [
                "class" => "success",
                "message" => "The article {$article->getTitle()} has been successfully deleted."
            ]
        ]);
    }
}
