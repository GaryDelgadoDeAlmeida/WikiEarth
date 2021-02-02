<?php

namespace App\Controller;

use App\Form\{UserType, LivingThingType, ArticleLivingThingType};
use App\Entity\{LivingThing, Notification, ArticleLivingThing};
use App\Manager\{UserManager, LivingThingManager, MediaGalleryManager, NotificationManager, ArticleLivingThingManager};
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
    private $articleLivingThingManager;
    private $mediaGalleryManager;
    private $notificationManager;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $manager, ContainerInterface $container)
    {
        $this->current_logged_user = $tokenStorage->getToken()->getUser();
        $this->userManager = new UserManager();
        $this->livingThingManager = new LivingThingManager($container);
        $this->articleLivingThingManager = new ArticleLivingThingManager();
        $this->mediaGalleryManager = new MediaGalleryManager($container);
        $this->notificationManager = new NotificationManager($manager);
        $this->manager = $manager;
    }

    /**
     * @Route("/user", name="userHome")
     */
    public function user_home()
    {
        $offset = 1;
        $limit = 4;
        
        return $this->render('user/home/index.html.twig', [
            "nbrAnimalia" => $this->getDoctrine()->getRepository(LivingThing::class)->countLivingThingKingdom('Animalia'),
            "nbrPlantae" => $this->getDoctrine()->getRepository(LivingThing::class)->countLivingThingKingdom('Plantae'),
            "nbrInsecta" => $this->getDoctrine()->getRepository(LivingThing::class)->countLivingThingKingdom('Insecta'),
            "nbrBacteria" => $this->getDoctrine()->getRepository(LivingThing::class)->countLivingThingKingdom('Bacteria'),
            "recent_posts" => $this->getDoctrine()->getRepository(ArticleLivingThing::class)->getArticleLivingThingsDesc($offset, $limit),
            "notifications" => $this->getDoctrine()->getRepository(Notification::class)->getLatestNotifications($this->current_logged_user->getId(), $offset, $limit),
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
            $livingThing = $this->getDoctrine()->getRepository(LivingThing::class)->getLivingThings($offset, $limit);
            $nbrPages = ceil($this->getDoctrine()->getRepository(LivingThing::class)->countLivingThings() / $limit);
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

        if($formLivingThing->isSubmitted() && $formLivingThing->isValid()) {
            if(empty($this->manager->getRepository(LivingThing::class)->getLivingThingByName($livingThing->getName()))) {
                $this->livingThingManager->setLivingThing(
                    $formLivingThing["imgPath"]->getData(), 
                    $livingThing, 
                    $this->manager
                );

                // TODO : On envoi une notification / un email aux admins du site
            } else {
                // On envoi une notif à l'utilisateur l'avertissant que le living thing qu'il a tenté d'ajouté existe déjà
                $this->notificationManager->livingThingAlreadyExist($this->current_logged_user);
                return $this->redirectToRoute("userLivingThing");
            }
        }

        return $this->render('user/living_thing/edit.html.twig', [
            "formLivingThing" => $formLivingThing->createView()
        ]);
    }

    /**
     * @Route("/user/living-thing/{id}/article", name="userLivingThingCreateArticle")
     */
    public function user_living_thing_create_article($id, Request $request)
    {
        $articleLivingThing = $this->getDoctrine()->getRepository(ArticleLivingThing::class)->findOneBy(["idLivingThing" => $id]);

        if(empty($articleLivingThing)) {
            $livingThing = $this->getDoctrine()->getRepository(LivingThing::class)->getLivingThing($id);

            if(!empty($livingThing)) {
                $articleLivingThing = new ArticleLivingThing();
                $formArticle = $this->createForm(ArticleLivingThingType::class, $articleLivingThing);
                $formArticle->get('livingThing')->setData($livingThing);
                $formArticle->handleRequest($request);

                if($formArticle->isSubmitted() && $formArticle->isValid()) {

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
                        $this->current_logged_user
                    );

                    // Une fois le traitement du living thing et de l'article, on traite les médias (qui seront liée à l'article)
                    $this->mediaGalleryManager->setMediaGalleryLivingThing(
                        $formArticle["mediaGallery"]->getData(),
                        $articleLivingThing,
                        $this->manager
                    );

                    // On envoie une notification à l'utilisateur
                    $this->notificationManager->userCreateArticle($this->current_logged_user);

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

        return $this->render('user/article/add.html.twig', [
            "formArticle" => $formArticle->createView()
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

        return $this->render('user/article/index.html.twig', [
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

        return $this->render('user/article/add.html.twig', [
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

            // On envoie une notification à l'utilisateur l'avertissant de la demande de mise à jour de l'article
            $this->notificationManager->userUpdateArticle($this->current_logged_user);
        }

        return $this->render('user/article/add.html.twig', [
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

    /**
     * @Route("/user/chat", name="userChat")
     */
    public function user_chat()
    {
        return $this->render('user/chat/index.html.twig');
    }
}
