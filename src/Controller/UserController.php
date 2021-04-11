<?php

namespace App\Controller;

use App\Form\{UserType, LivingThingType, ElementType, MineralType, Contact, ArticleLivingThingType, ArticleElementType, ArticleMineralType};
use App\Entity\{LivingThing, Element, Mineral, Notification, ArticleLivingThing, ArticleElement, ArticleMineral};
use App\Manager\{UserManager, LivingThingManager, ElementManager, MineralManager, ReferenceManager, MediaGalleryManager, ContactManager, NotificationManager, ArticleLivingThingManager, ArticleElementManager, ArticleMineralManager};
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserController extends AbstractController
{
    private $current_logged_user;
    private $manager;

    // Manager
    private $userManager;
    private $livingThingManager;
    private $elementManager;
    private $mineralManager;
    private $referenceManager;
    private $notificationManager;
    private $contactManager;
    private $articleLivingThingManager;
    private $articleElementManager;
    private $articleMineralManager;
    private $mediaGalleryManager;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $manager, ContainerInterface $container)
    {
        $this->current_logged_user = $tokenStorage->getToken()->getUser();
        $this->userManager = new UserManager();
        $this->livingThingManager = new LivingThingManager($container);
        $this->elementManager = new ElementManager($container);
        $this->mineralManager = new MineralManager($container);
        $this->referenceManager = new ReferenceManager();
        $this->notificationManager = new NotificationManager($manager);
        $this->contactManager = new ContactManager();
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

        $em = $this->getDoctrine();
        // $livingThingRepo = $em->getRepository(LivingThing::class);
        
        return $this->render('user/home/index.html.twig', [
            // "nbrAnimalia" => $livingThingRepo->countLivingThingKingdom('Animalia'),
            // "nbrPlantae" => $livingThingRepo->countLivingThingKingdom('Plantae'),
            // "nbrInsecta" => $livingThingRepo->countLivingThingKingdom('Insecta'),
            // "nbrBacteria" => $livingThingRepo->countLivingThingKingdom('Bacteria'),
            "nbrElement" => $em->getRepository(Element::class)->countElements(),
            "nbrMineral" => $em->getRepository(Mineral::class)->countMinerals(),
            "recent_posts" => $em->getRepository(ArticleLivingThing::class)->getArticleLivingThingsDesc($offset, $limit),
            "notifications" => $em->getRepository(Notification::class)->getLatestNotifications($this->current_logged_user->getId(), $offset, $limit),
            "recent_conversation" => [],
        ]);
    }

    /**
     * @Route("/user/profile", name="userProfile")
     */
    public function user_profil(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = $this->current_logged_user;
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
            "user" => $this->current_logged_user,
            "userImg" => $this->current_logged_user->getImgPath() ? $this->current_logged_user->getImgPath() : "https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1024px-User_icon_2.svg.png",
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
                    $livingThing = $this->getDoctrine()->getRepository(LivingThing::class)->getLivingThingWithArticle($offset, $limit);
                    $nbrPages = $this->getDoctrine()->getRepository(LivingThing::class)->countLivingThingWithArticle();
                } else {
                    $livingThing = $this->getDoctrine()->getRepository(LivingThing::class)->getLivingThingWithoutArticle($offset, $limit);
                    $nbrPages = $this->getDoctrine()->getRepository(LivingThing::class)->countLivingThingWithoutArticle();
                }
            } elseif($categoryBy != "all" && array_key_exists($categoryBy, $categoryChoices)) {
                $request->attributes->set("filter-by-livingThing", "all");

                $livingThing = $this->getDoctrine()->getRepository(LivingThing::class)->getLivingThingKingdom(\ucfirst($categoryBy), $offset, $limit);
                $nbrPages = $this->getDoctrine()->getRepository(LivingThing::class)->countLivingThingKingdom(\ucfirst($categoryBy));
            } else {
                $livingThing = $this->getDoctrine()->getRepository(LivingThing::class)->getLivingThings($offset, $limit);
                $nbrPages = ceil($this->getDoctrine()->getRepository(LivingThing::class)->countLivingThings() / $limit);
            }
        } else {
            $categoryBy = "all";
            $filterBy = "all";
            $livingThing = $this->getDoctrine()->getRepository(LivingThing::class)->searchLivingThing($search, $offset, $limit);
            $nbrPages = ceil($this->getDoctrine()->getRepository(LivingThing::class)->countSearchLivingThing($search) / $limit);
        }

        return $this->render('user/living_thing/index.html.twig', [
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
                    $this->contactManager->sendMail($this->current_logged_user->getEmail(), "New article {$livingThing->getName()}", "A new article has been created. Please, go to the back office to approuve or delete the article.");
                }
            } else {
                // On envoi une notif à l'utilisateur l'avertissant que le living thing qu'il a tenté d'ajouté existe déjà
                $this->notificationManager->livingThingAlreadyExist($this->current_logged_user);
                return $this->redirectToRoute("userLivingThing");
            }
        }

        return $this->render('user/living_thing/edit.html.twig', [
            "formLivingThing" => $formLivingThing->createView(),
            "response" => $message
        ]);
    }

    /**
     * @Route("/user/living-thing/{id}/article", name="userLivingThingCreateArticle")
     */
    public function user_living_thing_create_article($id, Request $request)
    {
        $articleLivingThing = $this->getDoctrine()->getRepository(ArticleLivingThing::class)->findOneBy(["idLivingThing" => $id]);
        $message = [];

        if(empty($articleLivingThing)) {
            $livingThing = $this->getDoctrine()->getRepository(LivingThing::class)->getLivingThing($id);

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
                                $this->current_logged_user
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
                    $this->notificationManager->userCreateArticle($this->current_logged_user);
                    $this->contactManager->sendMail($this->current_logged_user->getEmail(), "New article {$livingThing->getName()}", "A new article has been created. Please, go to the back office to approuve or delete the article.");
                    return $this->redirectToRoute("userLivingThing");
                }
            } else {
                // On envoi une notif à l'utilisateur l'avertissant que le living thing qu'il a tenté d'ajouté n'existe pas
                $this->notificationManager->livingThingNotFound($this->current_logged_user);

                return $this->redirectToRoute("404Error");
            }
        } else {
            // On envoi une notif à l'utilisateur l'avertissant que le living thing possède déjà un article
            $this->notificationManager->articleAlreadyExist($this->current_logged_user);

            return $this->redirectToRoute("403Error");
        }

        return $this->render('user/article/living-things/add.html.twig', [
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

        if($formLivingThing->isSubmitted() && $formLivingThing->isValid()) {

            // On effectue en premier le traitement sur le living thing
            $this->livingThingManager->setLivingThing(
                $formLivingThing["imgPath"]->getData(), 
                $livingThing, 
                $this->manager
            );
        }

        return $this->render('user/living_thing/edit.html.twig', [
            "formLivingThing" => $formLivingThing->createView()
        ]);
    }

    /**
     * @Route("/user/mineral", name="userMineral")
     */
    public function user_mineral(Request $request)
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
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
                    $minerals = $this->getDoctrine()->getRepository(Mineral::class)->getMineralsWithArticle($offset, $limit);
                    $nbrPages = ceil($this->getDoctrine()->getRepository(Mineral::class)->countMineralsWithArticle() / $limit);
                } elseif($filterBy == "not-have-article") {
                    $minerals = $this->getDoctrine()->getRepository(Mineral::class)->getMineralsWithoutArticle($offset, $limit);
                    $nbrPages = ceil($this->getDoctrine()->getRepository(Mineral::class)->countMineralsWithoutArticle() / $limit);
                }
            } else {
                $minerals = $this->getDoctrine()->getRepository(Mineral::class)->getMinerals($offset, $limit);
                $nbrPages = ceil($this->getDoctrine()->getRepository(Mineral::class)->countMinerals() / $limit);
            }
        } else {
            $filterBy = "all";
            $minerals = $this->getDoctrine()->getRepository(Mineral::class)->searchMineral($search, $offset, $limit);
            $nbrPages = ceil($this->getDoctrine()->getRepository(Mineral::class)->countSearchMineral($search) / $limit);
        }

        return $this->render('user/minerals/index.html.twig', [
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

        if($formMineral->isSubmitted() && $formMineral->isValid()) {
            if(empty($this->manager->getRepository(Mineral::class)->getMineralByName($mineral->getName()))) {
                $mineral->setImaStatus(explode(", ", $formArticle["mineral"]['imaStatus']));

                $this->mineralManager->setMineral(
                    $formMineral["imgPath"]->getData(), 
                    $mineral, 
                    $this->manager
                );

                // TODO : On envoi une notification / un email aux admins du site
                $this->notificationManager->userCreateArticle($this->current_logged_user);
                return $this->redirectToRoute("userMineral");
            } else {
                // On envoi une notif à l'utilisateur l'avertissant que le living thing qu'il a tenté d'ajouté existe déjà
                $this->notificationManager->mineralAlreadyExist($this->current_logged_user);
                return $this->redirectToRoute("userMineral");
            }
        }

        return $this->render('user/minerals/edit.html.twig', [
            "formMineral" => $formMineral->createView()
        ]);
    }

    /**
     * @Route("/user/mineral/{id}/article", name="userMineralCreateArticle")
     */
    public function user_mineral_create_article($id, Request $request)
    {
        $articleMineral = $this->getDoctrine()->getRepository(ArticleMineral::class)->findOneBy(["mineral" => $id]);

        if(empty($articleMineral)) {
            $mineral = $this->getDoctrine()->getRepository(Mineral::class)->find($id);

            if(!empty($mineral)) {
                $articleMineral = new ArticleMineral();
                $formArticle = $this->createForm(ArticleMineralType::class, $articleMineral);
                $formArticle->get('mineral')->setData($mineral);
                $formArticle->get('mineral')->get('imaStatus')->setData(implode(", ", $mineral->getImaStatus()));
                $formArticle->handleRequest($request);

                if($formArticle->isSubmitted() && $formArticle->isValid()) {

                    $mineral->setImaStatus(explode(", ", $formArticle["mineral"]['imaStatus']->getData()));

                    // On effectue en premier le traitement sur le living thing
                    $this->mineralManager->setMineral(
                        $formArticle["mineral"]["imgPath"]->getData(),
                        $mineral,
                        $formArticle["mineral"],
                        $this->manager
                    );

                    // On traite maintenant l'article (pour cause ces liaisons avec les autres tables)
                    $this->articleMineralManager->setArticleMineral(
                        $articleMineral,
                        $mineral,
                        $this->manager,
                        $this->current_logged_user
                    );

                    // Une fois le traitement du living thing et de l'article, on traite les médias (qui seront liée à l'article)
                    $this->mediaGalleryManager->setMediaGalleryMinerals(
                        $formArticle["mediaGallery"]->getData(),
                        $articleMineral,
                        $this->manager
                    );

                    // On envoie une notification à l'utilisateur
                    $this->notificationManager->userCreateArticle($this->current_logged_user);
                    return $this->redirectToRoute("userMineral");
                }

                return $this->render('user/article/minerals/add.html.twig', [
                    "formArticle" => $formArticle->createView()
                ]);
            } else {
                // On envoi une notif à l'utilisateur l'avertissant que le living thing qu'il a tenté d'ajouté n'existe pas
                $this->notificationManager->mineralNotFound($this->current_logged_user);
                return $this->redirectToRoute("404Error");
            }
        } else {
            // On envoi une notif à l'utilisateur l'avertissant que le living thing possède déjà un article
            $this->notificationManager->articleAlreadyExist($this->current_logged_user);

            return $this->redirectToRoute("403Error");
        }
    }

    /**
     * @Route("/user/mineral/{id}/edit", name="userEditArticleMineral")
     */
    public function user_edit_mineral(Mineral $mineral, Request $request)
    {
        $formMineral = $this->createForm(MineralType::class, $mineral);
        $formMineral->get('imaStatus')->setData(implode(", ", $mineral->getImaStatus()));
        $formMineral->handleRequest($request);
        $response = [];

        if($formMineral->isSubmitted() && $formMineral->isValid()) {
            $response = $this->mineralManager->setMineral(
                $formMineral["imgPath"]->getData(), 
                $mineral,
                $formMineral,
                $this->manager
            );
        }

        return $this->render('user/minerals/edit.html.twig', [
            "formMineral" => $formMineral->createView(),
            "response" => $response
        ]);
    }

    /**
     * @Route("/user/element", name="userElement")
     */
    public function user_element(Request $request)
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $search = !empty($request->get("search")) ? $request->get("search") : null;
        $filterBy = !empty($request->get("filter-by-mineral")) ? $request->get("filter-by-mineral") : "all";
        $filterChocies = [
            "all" => "All",
            "have-article" => "Have an article",
            "not-have-article" => "Not have an article"
        ];
        $elements = [];
        $nbrPages = 1;
        
        if(empty($search)) {
            if($filterBy != "all" && array_key_exists($filterBy, $filterChocies)) {
                if($filterBy == "have-article") {
                    $minerals = $this->getDoctrine()->getRepository(Element::class)->getElementsWithArticle($offset, $limit);
                    $nbrPages = ceil($this->getDoctrine()->getRepository(Element::class)->countElementsWithArticle() / $limit);
                } elseif($filterBy == "not-have-article") {
                    $minerals = $this->getDoctrine()->getRepository(Element::class)->getElementsWithoutArticle($offset, $limit);
                    $nbrPages = ceil($this->getDoctrine()->getRepository(Element::class)->countElementsWithoutArticle() / $limit);
                }
            } else {
                $minerals = $this->getDoctrine()->getRepository(Element::class)->getElements($offset, $limit);
                $nbrPages = ceil($this->getDoctrine()->getRepository(Element::class)->countElements() / $limit);
            }
        } else {
            $filterBy = "all";
            $elements = $this->getDoctrine()->getRepository(Element::class)->searchElement($search, $offset, $limit);
            $nbrPages = ceil($this->getDoctrine()->getRepository(Element::class)->countSearchElement($search) / $limit);
        }

        return $this->render('user/elements/index.html.twig', [
            "elements" => $elements,
            "search" => $search,
            "offset" => $offset,
            "total_page" => $nbrPages,
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
            $articleLivingThing = $this->getDoctrine()->getRepository(ArticleLivingThing::class)->searchArticleLivingThings($search);
            $nbrPages = ceil($this->getDoctrine()->getRepository(ArticleLivingThing::class)->countSearchArticleLivingThings() / $limit);
        } else {
            if($category_by == "all") {
                $articleLivingThing = $this->getDoctrine()->getRepository(ArticleLivingThing::class)->getArticleLivingThingsApproved($offset, $limit);
                $nbrPages = ceil($this->getDoctrine()->getRepository(ArticleLivingThing::class)->countArticleLivingThingsApproved() / $limit);
            } else {
                $articleLivingThing = $this->getDoctrine()->getRepository(ArticleLivingThing::class)->getArticleLivingThingsByLivingThingKingdom($category_by, $offset, $limit);
                $nbrPages = ceil($this->getDoctrine()->getRepository(ArticleLivingThing::class)->countArticleLivingThingsByKingdom($category_by, $limit));
            }
        }

        return $this->render('user/article/living-things/index.html.twig', [
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

        if($formArticle->isSubmitted() && $formArticle->isValid()) {
            $livingThing = $formArticle["livingThing"]->getData();
            
            // On effectue en premier le traitement sur le living thing
            $this->livingThingManager->setLivingThing(
                $formArticle["livingThing"]['imgPath']->getData(),
                $livingThing,
                $this->manager
            );

            // On traite maintenant l'article (pour cause ces liaisons avec les autres tables)
            $this->articleLivingThingManager->setArticleLivingThing(
                $article, 
                $livingThing,
                $this->manager, 
                $this->current_logged_user
            );

            // Une fois le traitement du living thing et de l'article, on traite les médias (qui seront liée à l'article)
            $this->mediaGalleryManager->setMediaGalleryLivingThing(
                $formArticle["mediaGallery"]->getData(),
                $articleLivingThing,
                $this->manager
            );

            // On notifie que l'utilisateur vient de créer un nouvel article et que nous allons le vérifier
            $this->notificationManager->userCreateArticle($this->current_logged_user);
        }

        return $this->render('user/article/living-things/add.html.twig', [
            "formArticle" => $formArticle->createView()
        ]);
    }

    /**
     * @Route("/user/article/{id}/edit", name="userEditArticle")
     */
    public function user_edit_article(ArticleLivingThing $article, Request $request)
    {
        $formArticle = $this->createForm(ArticleLivingThingType::class, $article);
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
                $article, 
                $livingThing,
                $this->manager, 
                $this->current_logged_user
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
            $this->notificationManager->userUpdateArticle($this->current_logged_user);
        }

        return $this->render('user/article/living-things/add.html.twig', [
            "formArticle" => $formArticle->createView()
        ]);
    }

    /**
     * @Route("/user/notifications", name="userNotifs")
     */
    public function user_notifications(Request $request)
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $notifications = $this->getDoctrine()->getRepository(Notification::class)->getLatestNotifications($this->current_logged_user->getId(), $offset, $limit);
        $totalPage = ceil($this->getDoctrine()->getRepository(Notification::class)->countNotification($this->current_logged_user->getId()) / $limit);

        return $this->render('user/notifications/index.html.twig', [
            "notifications" => $notifications,
            "offset" => $offset,
            "nbrPage" => $totalPage,
        ]);
    }
}
