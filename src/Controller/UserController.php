<?php

namespace App\Controller;

use App\Form\{UserType, LivingThingType, ElementType, MineralType, Contact, ArticleLivingThingType, ArticleElementType, ArticleMineralType};
use App\Entity\{LivingThing, Element, Mineral, Notification, Article, ArticleLivingThing, ArticleElement, ArticleMineral};
use App\Manager\{UserManager, LivingThingManager, ElementManager, MineralManager, ReferenceManager, MediaGalleryManager, ContactManager, NotificationManager, ArticleManager, ArticleLivingThingManager, ArticleElementManager, ArticleMineralManager};
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserController extends AbstractController
{
    private $currentLoggedUser;
    private $manager;

    // Manager
    private $userManager;
    private $livingThingManager;
    private $elementManager;
    private $mineralManager;
    private $referenceManager;
    private $notificationManager;
    private $contactManager;
    private $articleManager;
    private $articleLivingThingManager;
    private $articleElementManager;
    private $articleMineralManager;
    private $mediaGalleryManager;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $manager, ContainerInterface $container)
    {
        $this->currentLoggedUser = $tokenStorage->getToken()->getUser();
        $this->userManager = new UserManager();
        $this->livingThingManager = new LivingThingManager($container);
        $this->elementManager = new ElementManager($container);
        $this->mineralManager = new MineralManager($container);
        $this->referenceManager = new ReferenceManager();
        $this->notificationManager = new NotificationManager($manager);
        $this->contactManager = new ContactManager();
        $this->articleManager = new ArticleManager();
        $this->articleLivingThingManager = new ArticleLivingThingManager();
        $this->articleElementManager = new ArticleElementManager();
        $this->articleMineralManager = new ArticleMineralManager();
        $this->mediaGalleryManager = new MediaGalleryManager($container);
        $this->manager = $manager;
    }

    /**
     * @Route("/user", name="userHome")
     */
    public function user_home()
    {
        $offset = 1;
        $limit = 4;
        // $livingThingRepo = $this->manager->getRepository(LivingThing::class);
        
        return $this->render('user/home/index.html.twig', [
            // "nbrAnimalia" => $livingThingRepo->countLivingThingKingdom('Animalia'),
            // "nbrPlantae" => $livingThingRepo->countLivingThingKingdom('Plantae'),
            // "nbrInsecta" => $livingThingRepo->countLivingThingKingdom('Insecta'),
            // "nbrBacteria" => $livingThingRepo->countLivingThingKingdom('Bacteria'),
            "nbrElement" => $this->manager->getRepository(Element::class)->countElements(),
            "nbrMineral" => $this->manager->getRepository(Mineral::class)->countMinerals(),
            "recent_posts" => $this->manager->getRepository(Article::class)->getArticlesApproved($offset, $limit),
            "notifications" => $this->manager->getRepository(Notification::class)->getLatestNotifications($this->currentLoggedUser->getId(), $offset, $limit),
            "recent_conversation" => [],
        ]);
    }

    /**
     * @Route("/user/profile", name="userProfile")
     */
    public function user_profil(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = $this->currentLoggedUser;
        $formUser = $this->createForm(UserType::class, $user);
        $formUser->handleRequest($request);

        if($formUser->isSubmitted() && $formUser->isValid()) {

            // On mets à jour les informations de l'utilisateur (nom, prénom, img, pass, etc ...)
            $this->userManager->updateUser(
                $formUser, 
                $user, 
                $this->manager, 
                $encoder, 
                $this->getParameter('project_users_dir')
            );
        }
        
        return $this->render('user/profile/index.html.twig', [
            "user" => $this->currentLoggedUser,
            "userImg" => $this->currentLoggedUser->getImgPath() ? $this->currentLoggedUser->getImgPath() : "https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1024px-User_icon_2.svg.png",
            "formUser" => $formUser->createView()
        ]);
    }

    /**
     * @Route("/user/living-thing", name="userLivingThing")
     */
    public function user_living_thing(Request $request)
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $search = !empty($request->get("search")) ? $request->get("search") : null;
        $filterBy = !empty($request->get("filter-by-livingThing")) ? $request->get("filter-by-livingThing") : "all";
        $categoryBy = !empty($request->get("category-by-livingThing")) ? $request->get("category-by-livingThing") : "all";
        $categoryChoices = [
            "all" => "All",
            "animalia" => "Animals",
            "plantae" => "Plants",
            "insecta" => "Insecta",
            "bacteria" => "Bacteria",
            "virus" => "Virus"
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
                    $livingThing = $this->manager->getRepository(LivingThing::class)->getLivingThingWithArticle($offset, $limit);
                    $nbrPages = $this->manager->getRepository(LivingThing::class)->countLivingThingWithArticle();
                } else {
                    $livingThing = $this->manager->getRepository(LivingThing::class)->getLivingThingWithoutArticle($offset, $limit);
                    $nbrPages = $this->manager->getRepository(LivingThing::class)->countLivingThingWithoutArticle();
                }
            } elseif($categoryBy != "all" && array_key_exists($categoryBy, $categoryChoices)) {
                $request->attributes->set("filter-by-livingThing", "all");

                $livingThing = $this->manager->getRepository(LivingThing::class)->getLivingThingKingdom(\ucfirst($categoryBy), $offset, $limit);
                $nbrPages = $this->manager->getRepository(LivingThing::class)->countLivingThingKingdom(\ucfirst($categoryBy));
            } else {
                $livingThing = $this->manager->getRepository(LivingThing::class)->getLivingThings($offset, $limit);
                $nbrPages = ceil($this->manager->getRepository(LivingThing::class)->countLivingThings() / $limit);
            }
        } else {
            $categoryBy = "all";
            $filterBy = "all";
            $livingThing = $this->manager->getRepository(LivingThing::class)->searchLivingThing($search, $offset, $limit);
            $nbrPages = ceil($this->manager->getRepository(LivingThing::class)->countSearchLivingThing($search) / $limit);
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
     * @Route("/user/living-thing/add", name="userAddLivingThing")
     */
    public function user_add_living_thing(Request $request)
    {
        $livingThing = new LivingThing();
        $formLivingThing = $this->createForm(LivingThingType::class, $livingThing);
        $formLivingThing->handleRequest($request);
        $message = [];

        if($formLivingThing->isSubmitted() && $formLivingThing->isValid()) {
            if(empty($this->manager->getRepository(LivingThing::class)->getLivingThingByName($livingThing->getName()))) {
                $message = $this->livingThingManager->setLivingThing(
                    $formLivingThing["imgPath"]->getData(), 
                    $livingThing, 
                    $this->manager
                );

                if(empty($message) || $message["erorr"] == false) {
                    // TODO : On envoi une notification / un email aux admins du site
                    $this->contactManager->sendMail($this->currentLoggedUser->getEmail(), "New article {$livingThing->getName()}", "A new article has been created. Please, go to the back office to approuve or delete the article.");
                }
            } else {
                // On envoi une notif à l'utilisateur l'avertissant que le living thing qu'il a tenté d'ajouté existe déjà
                $this->notificationManager->livingThingAlreadyExist($this->currentLoggedUser);
                return $this->redirectToRoute("userLivingThing");
            }
        }

        return $this->render('user/article/living-things/formLivingThing.html.twig', [
            "formLivingThing" => $formLivingThing->createView(),
            "response" => $message
        ]);
    }

    /**
     * @Route("/user/living-thing/{id}/article", name="userLivingThingCreateArticle")
     */
    public function user_living_thing_create_article($id, Request $request)
    {
        $articleLivingThing = $this->manager->getRepository(Article::class)->getArticleByLivingThing($id);
        $message = [];

        if(empty($articleLivingThing)) {
            $livingThing = $this->manager->getRepository(LivingThing::class)->getLivingThing($id);

            if(!empty($livingThing)) {
                $articleLivingThing = new ArticleLivingThing();
                $formArticle = $this->createForm(ArticleLivingThingType::class, $articleLivingThing);
                $formArticle->get('livingThing')->setData($livingThing);
                $formArticle->handleRequest($request);

                if($formArticle->isSubmitted() && $formArticle->isValid()) {
                    $existingLivingThing = $this->em->getRepository(LivingThing::class)->getLivingThingByName($livingThing->getName());
                    if(!empty($existingLivingThing)) {
                        // On effectue en premier le traitement sur le living thing
                        $message = $this->livingThingManager->setLivingThing(
                            $formArticle["livingThing"]["imgPath"]->getData(),
                            $livingThing,
                            $this->manager
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
                                $this->manager,
                                $this->currentLoggedUser
                            );

                            // Une fois le traitement du living thing et de l'article, on traite les médias (qui seront liée à l'article)
                            $this->mediaGalleryManager->setMediaGalleryLivingThing(
                                $formArticle["mediaGallery"]->getData(),
                                $articleLivingThing,
                                $this->manager
                            );
                        } else {
                            $message = [
                                "error" => true,
                                "class" => "danger",
                                "message" => "L'être vivant {$livingThing->getName()} possède déjà un article. L'ajout de ce nouvel article est annulé."
                            ];
                        }
                    }

                    // On envoie une notification à l'utilisateur
                    $this->notificationManager->userCreateArticle($this->currentLoggedUser);
                    $this->contactManager->sendMail($this->currentLoggedUser->getEmail(), "New article {$livingThing->getName()}", "A new article has been created. Please, go to the back office to approuve or delete the article.");
                    return $this->redirectToRoute("userLivingThing");
                }
            } else {
                // On envoi une notif à l'utilisateur l'avertissant que le living thing qu'il a tenté d'ajouté n'existe pas
                $this->notificationManager->livingThingNotFound($this->currentLoggedUser);

                return $this->redirectToRoute("userLivingThing", [
                    "class" => "danger",
                    "message" => "This living thing does not exist."
                ], 307);
            }
        } else {
            // On envoi une notif à l'utilisateur l'avertissant que le living thing possède déjà un article
            $this->notificationManager->articleAlreadyExist($this->currentLoggedUser);

            return $this->redirectToRoute("userLivingThing", [
                "class" => "danger",
                "message" => "This living thing already have an article."
            ], 307);
        }

        return $this->render('user/article/living-things/formArticle.html.twig', [
            "formArticle" => $formArticle->createView(),
            "response" => $message
        ]);
    }

    /**
     * @Route("/user/living-thing/{id}/edit", name="userEditLivingThing")
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
                $livingThing, 
                $this->manager
            );
        }

        return $this->render('user/article/living-things/formLivingThing.html.twig', [
            "formLivingThing" => $formLivingThing->createView(),
            "response" => $response
        ]);
    }

    /**
     * @Route("/user/mineral", name="userMineral")
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
                    $minerals = $this->manager->getRepository(Mineral::class)->getMineralsWithArticle($offset, $limit);
                    $nbrPages = ceil($this->manager->getRepository(Mineral::class)->countMineralsWithArticle() / $limit);
                } elseif($filterBy == "not-have-article") {
                    $minerals = $this->manager->getRepository(Mineral::class)->getMineralsWithoutArticle($offset, $limit);
                    $nbrPages = ceil($this->manager->getRepository(Mineral::class)->countMineralsWithoutArticle() / $limit);
                }
            } else {
                $minerals = $this->manager->getRepository(Mineral::class)->getMinerals($offset, $limit);
                $nbrPages = ceil($this->manager->getRepository(Mineral::class)->countMinerals() / $limit);
            }
        } else {
            $filterBy = "all";
            $minerals = $this->manager->getRepository(Mineral::class)->searchMineral($search, $offset, $limit);
            $nbrPages = ceil($this->manager->getRepository(Mineral::class)->countSearchMineral($search) / $limit);
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
     * @Route("/user/mineral/add", name="userAddMineral")
     */
    public function user_add_mineral(Request $request)
    {
        $mineral = new Mineral();
        $formMineral = $this->createForm(MineralType::class, $mineral);
        $formMineral->handleRequest($request);
        $response = [];

        if($formMineral->isSubmitted() && $formMineral->isValid()) {
            if(empty($this->manager->getRepository(Mineral::class)->getMineralByName($mineral->getName()))) {
                $mineral->setImaStatus(explode(", ", $formMineral['imaStatus']->getData()));

                $response = $this->mineralManager->setMineral(
                    $formMineral["imgPath"]->getData(), 
                    $mineral, 
                    $formMineral,
                    $this->manager
                );

                // TODO : On envoi une notification / un email aux admins du site
                $this->notificationManager->userCreateArticle($this->currentLoggedUser);
                return $this->redirectToRoute("userMineral");
            } else {
                // On envoi une notif à l'utilisateur l'avertissant que le living thing qu'il a tenté d'ajouté existe déjà
                $this->notificationManager->mineralAlreadyExist($this->currentLoggedUser);
                return $this->redirectToRoute("userMineral");
            }
        }

        return $this->render('user/article/minerals/formMineral.html.twig', [
            "formMineral" => $formMineral->createView(),
            "response" => $response
        ]);
    }

    /**
     * @Route("/user/mineral/{id}/article", name="userMineralCreateArticle")
     */
    public function user_mineral_create_article($id, Request $request)
    {
        $articleMineral = $this->manager->getRepository(ArticleMineral::class)->findOneBy(["mineral" => $id]);

        if(empty($articleMineral)) {
            $mineral = $this->manager->getRepository(Mineral::class)->find($id);

            if(!empty($mineral)) {
                $articleMineral = new ArticleMineral();
                $formArticle = $this->createForm(ArticleMineralType::class, $articleMineral);
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
                        $formArticle["mineral"],
                        $this->manager
                    );

                    // On traite maintenant l'articleMineral (pour cause ces liaisons avec les autres tables)
                    $response = $this->articleMineralManager->setArticleMineral(
                        $articleMineral,
                        $mineral,
                        $this->manager
                    );

                    // On traite maintenant l'article (pour cause ces liaisons avec les autres tables)
                    $response = $this->articleManager->insertArticle(
                        $articleMineral,
                        $this->manager,
                        $this->currentLoggedUser
                    );

                    // Une fois le traitement du living thing et de l'article, on traite les médias (qui seront liée à l'article)
                    $response = $this->mediaGalleryManager->setMediaGalleryMinerals(
                        $formArticle["mediaGallery"]->getData(),
                        $articleMineral,
                        $this->manager
                    );

                    // On envoie une notification à l'utilisateur
                    $this->notificationManager->userCreateArticle($this->currentLoggedUser);
                    return $this->redirectToRoute("userMineral");
                }

                return $this->render('user/article/minerals/formArticle.html.twig', [
                    "formArticle" => $formArticle->createView(),
                    "response" => $response
                ]);
            } else {
                // On envoi une notif à l'utilisateur l'avertissant que le mineral qu'il a tenté d'ajouté n'existe pas
                $this->notificationManager->mineralNotFound($this->currentLoggedUser);
                return $this->redirectToRoute("userMineral", [
                    "class" => "danger",
                    "message" => "This mineral does not exist."
                ], 307);
            }
        } else {
            // On envoi une notif à l'utilisateur l'avertissant que le mineral possède déjà un article
            $this->notificationManager->articleAlreadyExist($this->currentLoggedUser);

            return $this->redirectToRoute("userMineral", [
                "class" => "danger",
                "message" => "This mineral already have an article."
            ], 307);
        }
    }

    /**
     * @Route("/user/element", name="userElement")
     */
    public function user_element(Request $request)
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $search = !empty($request->get("search")) ? $request->get("search") : null;
        $filterBy = !empty($request->get("filter-by-element")) ? $request->get("filter-by-element") : "all";
        $filterChoices = [
            "all" => "All",
            "have-article" => "Have an article",
            "not-have-article" => "Not have an article"
        ];
        $elements = [];
        $nbrPages = 1;
        
        if(empty($search)) {
            if($filterBy != "all" && array_key_exists($filterBy, $filterChoices)) {
                if($filterBy == "have-article") {
                    $elements = $this->manager->getRepository(Element::class)->getElementsWithArticle($offset, $limit);
                    $nbrPages = ceil($this->manager->getRepository(Element::class)->countElementsWithArticle() / $limit);
                } elseif($filterBy == "not-have-article") {
                    $elements = $this->manager->getRepository(Element::class)->getElementsWithoutArticle($offset, $limit);
                    $nbrPages = ceil($this->manager->getRepository(Element::class)->countElementsWithoutArticle() / $limit);
                }
            } else {
                $elements = $this->manager->getRepository(Element::class)->getElements($offset, $limit);
                $nbrPages = ceil($this->manager->getRepository(Element::class)->countElements() / $limit);
            }
        } else {
            $filterBy = "all";
            $elements = $this->manager->getRepository(Element::class)->searchElement($search, $offset, $limit);
            $nbrPages = ceil($this->manager->getRepository(Element::class)->countSearchElement($search) / $limit);
        }
        
        return $this->render('user/article/elements/listElement.html.twig', [
            "filterChoices" => $filterChoices,
            "filter_by" => $filterBy,
            "elements" => $elements,
            "search" => $search,
            "offset" => $offset,
            "total_page" => $nbrPages,
        ]);
    }

    /**
     * @Route("/user/element/{id}/article", name="userElementCreateArticle")
     */
    public function user_element_create_article(int $id, Request $request)
    {
        $element = $this->manager->getRepository(Element::class)->find($id);
        $response = [];
        
        if(empty($element)) {
            return $this->redirectToRoute("userElement", [
                "error" => true,
                "class" => "danger",
                "message" => "The element you tryied to access hasn't been found."
            ]);
        }

        if(!empty($element->getArticleElement())) {
            return $this->redirectToRoute("userElement", [
                "error" => true,
                "class" => "danger",
                "message" => "The element {$element->getName()} already have an article."
            ]);
        }

        $articleElement = new ArticleElement();
        $formArticle = $this->createForm(ArticleElementType::class, $articleElement);
        $formArticle->get("element")->setData($element);
        $formArticle->get("element")->get("volumicMass")->setData(implode(" || ", $element->getVolumicMass()));
        $formArticle->handleRequest($request);

        if($formArticle->isSubmitted() && $formArticle->isValid()) {
            $response = $this->elementManager->setElement(
                $formArticle["element"]["imgPath"]->getData(),
                $element,
                $formArticle["element"],
                $this->manager
            );

            if(!empty($response) && $response["error"] == false) {
                $response = $this->articleElementManager->setArticleElement(
                    $articleElement,
                    $element,
                    $this->manager
                );

                // Insert reference of the content of the article
                // $response = $this->referenceManager->setReferences(
                //     $formArticle["references"]->getData(),
                //     $articleElementManager,
                //     $this->manager
                // );

                // Working logic of new table 
                $response = $this->articleManager->insertArticle(
                    $articleElement, 
                    $this->manager, 
                    $this->currentLoggedUser
                );

                $this->notificationManager->userCreateArticle($this->currentLoggedUser);
                return $this->redirectToRoute("userElement", [
                    "response" => $response
                ], 302);
            }
        }

        return $this->render('user/article/elements/formArticle.html.twig', [
            "formArticle" => $formArticle->createView(),
            "response" => $response
        ]);
    }

    /**
     * @Route("/user/article", name="userArticle")
     */
    public function user_article(Request $request)
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
            $articleLivingThing = $this->manager->getRepository(Article::class)->searchArticleLivingThings($search);
            $nbrPages = ceil($this->manager->getRepository(Article::class)->countSearchArticleLivingThings() / $limit);
        } else {
            if($category_by == "all") {
                $articleLivingThing = $this->manager->getRepository(Article::class)->getArticleLivingThingsApproved($offset, $limit);
                $nbrPages = ceil($this->manager->getRepository(Article::class)->countArticleLivingThingsApproved() / $limit);
            } else {
                $articleLivingThing = $this->manager->getRepository(Article::class)->getArticleLivingThingsByLivingThingKingdom($category_by, $offset, $limit);
                $nbrPages = ceil($this->manager->getRepository(Article::class)->countArticleLivingThingsByKingdom($category_by, $limit));
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
     * @Route("/user/article/add", name="userAddArticle")
     */
    public function user_add_article(Request $request)
    {
        $article = new ArticleLivingThing();
        $formArticle = $this->createForm(ArticleLivingThingType::class, $article);
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
                $this->currentLoggedUser
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

            // On notifie que l'utilisateur vient de créer un nouvel article et que nous allons le vérifier
            $this->notificationManager->userCreateArticle($this->currentLoggedUser);
        }

        return $this->render('user/article/living-things/formArticle.html.twig', [
            "formArticle" => $formArticle->createView(),
            "response" => $response
        ]);
    }

    /**
     * @Route("/user/article/{category}/{id}/edit", name="userEditArticle")
     */
    public function user_edit_article(string $category, int $id, Request $request)
    {
        $response = [];

        if($category == "living-thing") {
            $article = $this->manager->getRepository(Article::class)->getArticleByLivingThing($id);
            
            if(!empty($article)) {
                $articleLivingThing = $article->getArticleLivingThing();
                $formArticle = $this->createForm(ArticleLivingThingType::class, $articleLivingThing);
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
                        $this->currentLoggedUser
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

                    // On envoie une notification à l'utilisateur l'avertissant de la demande de mise à jour de l'article
                    $this->notificationManager->userUpdateArticle($this->currentLoggedUser);
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
            $article = $this->manager->getRepository(Article::class)->getArticleByElement($id);
            
            if(!empty($article)) {
                $articleElement = $article->getArticleElement();
                $formElement = $this->createForm(ArticleElementType::class, $articleElement);
                $formElement->get("element")->setData($articleElement->getElement());
                $formElement->handleRequest($request);

                if($formElement->isSubmitted() && $formElement->isValid()) {
                    $response = $this->mineralElement->setElement(
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
            $article = $this->manager->getRepository(Article::class)->getArticleByMineral($id);
            
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
                        $this->currentLoggedUser
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

    /**
     * @Route("/user/notifications", name="userNotifs")
     */
    public function user_notifications(Request $request)
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $notifications = $this->manager->getRepository(Notification::class)->getLatestNotifications($this->currentLoggedUser->getId(), $offset, $limit);
        $totalPage = ceil($this->manager->getRepository(Notification::class)->countNotification($this->currentLoggedUser->getId()) / $limit);

        return $this->render('user/notifications/index.html.twig', [
            "notifications" => $notifications,
            "offset" => $offset,
            "nbrPage" => $totalPage,
        ]);
    }
}
