<?php

namespace App\Controller;

use App\Form\UserType;
use App\Entity\LivingThing;
use App\Manager\UserManager;
use App\Form\LivingThingType;
use Manager\LivingThingManager;
use App\Entity\ArticleLivingThing;
use App\Form\ArticleLivingThingType;
use Manager\ArticleLivingThingManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserController extends AbstractController
{
    private $current_logged_user;
    private $livingThingManager;
    private $articleLivingThingManager;
    private $userManager;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->current_logged_user = $tokenStorage->getToken()->getUser();
        $this->livingThingManager = new LivingThingManager();
        $this->articleLivingThingManager = new ArticleLivingThingManager();
        $this->userManager = new UserManager();
    }

    /**
     * @Route("/user", name="userHome")
     */
    public function user_home()
    {
        return $this->render('user/home/index.html.twig', [
            "nbrAnimalia" => $this->getDoctrine()->getRepository(LivingThing::class)->countLivingThingKingdom('Animalia'),
            "nbrPlantae" => $this->getDoctrine()->getRepository(LivingThing::class)->countLivingThingKingdom('Plantae'),
            "nbrInsecta" => $this->getDoctrine()->getRepository(LivingThing::class)->countLivingThingKingdom('Insecta'),
            "nbrBacteria" => $this->getDoctrine()->getRepository(LivingThing::class)->countLivingThingKingdom('Bacteria'),
            "recent_posts" => $this->getDoctrine()->getRepository(ArticleLivingThing::class)->getArticleLivingThingsDesc(1, 4),
            "notifications" => [],
            "recent_conversation" => [],
        ]);
    }

    /**
     * @Route("/user/profile", name="userProfile")
     */
    public function user_profil(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = $this->current_logged_user;
        $formUser = $this->createForm(UserType::class, $user);
        $formUser->handleRequest($request);

        if($formUser->isSubmitted() && $formUser->isValid()) {
            $this->userManager->updateUser(
                $formUser, 
                $user, 
                $manager, 
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
        $livingThing = [];
        $nbrPages = 1;
        
        if(empty($search)) {
            $livingThing = $this->getDoctrine()->getRepository(LivingThing::class)->getLivingThings($offset, $limit);
            $nbrPages = ceil($this->getDoctrine()->getRepository(LivingThing::class)->countLivingThings() / $limit);
        } else {
            $livingThing = $this->getDoctrine()->getRepository(LivingThing::class)->searchLivingThing($search, $offset, $limit);
            $nbrPages = ceil($this->getDoctrine()->getRepository(LivingThing::class)->countSearchLivingThing($search) / $limit);
        }

        return $this->render('user/living_thing/index.html.twig', [
            "livingThings" => $livingThing,
            "offset" => $offset,
            "total_page" => $nbrPages,
        ]);
    }

    /**
     * @Route("/user/living-thing/add", name="userAddLivingThing")
     */
    public function user_add_living_thing(Request $request, EntityManagerInterface $manager)
    {
        $livingThing = new LivingThing();
        $formLivingThing = $this->createForm(LivingThingType::class, $livingThing);
        $formLivingThing->handleRequest($request);

        if($formLivingThing->isSubmitted() && $formLivingThing->isValid()) {
            $this->livingThingManager->setLivingThing(
                $formLivingThing, 
                $livingThing, 
                $manager
            );
        }

        return $this->render('user/living_thing/edit.html.twig', [
            "formLivingThing" => $formLivingThing->createView()
        ]);
    }

    /**
     * @Route("/user/living-thing/{id}/article", name="userLivingThingCreateArticle")
     */
    public function user_living_thing_create_article($id, Request $request, EntityManagerInterface $manager)
    {
        $articleLivingThing = $this->getDoctrine()->getRepository(ArticleLivingThing::class)->getArticleLivingThing($id);

        if(empty($articleLivingThing)) {
            $articleLivingThing = new ArticleLivingThing();
            $livingThing = $this->getDoctrine()->getRepository(LivingThing::class)->getLivingThing($id);

            if(!empty($livingThing)) {
                $formArticle = $this->createForm(ArticleLivingThingType::class, $articleLivingThing);
                $formArticle->get('livingThing')->setData($livingThing);
                $formArticle->handleRequest($request);

                if($formArticle->isSubmitted() && $formArticle->isValid()) {
                    $this->articleLivingThingManager->setArticleLivingThing(
                        $articleLivingThing,
                        $livingThing,
                        $manager,
                        $this->current_logged_user
                    );
                    // $this->articleLivingThingManager->insertArticleLivingThing(
                    //     $articleLivingThing,
                    //     $livingThing,
                    //     $manager,
                    //     $this->current_logged_user
                    // );
                }
            } else {
                dd("L'identifiant {$id} n'existe pas.");
            }
        } else {
            dd("Il existe déjà un article sur cette être vivant.");
        }

        return $this->render('user/article/add.html.twig', [
            "formArticle" => $formArticle->createView()
        ]);
    }

    /**
     * @Route("/user/living-thing/{id}/edit", name="userEditLivingThing")
     */
    public function user_edit_living_thing(LivingThing $livingThing, Request $request, EntityManagerInterface $manager)
    {
        $formLivingThing = $this->createForm(LivingThingType::class, $livingThing);
        $formLivingThing->handleRequest($request);

        if($formLivingThing->isSubmitted() && $formLivingThing->isValid()) {
            $this->livingThingManager->setLivingThing(
                $formLivingThing, 
                $livingThing, 
                $manager
            );
        }

        return $this->render('user/living_thing/edit.html.twig', [
            "formLivingThing" => $formLivingThing->createView()
        ]);
    }

    /**
     * @Route("/user/living-thing/{id}/delete", name="userDeleteLivingThing")
     */
    public function user_delete_living_thing(LivingThing $livingThing, Request $request, EntityManagerInterface $manager)
    {
        $manager->remove($livingThing);
        $manager->flush();

        return $this->redirectToRoute('userLivingThing');
    }

    /**
     * @Route("/user/article", name="userArticle")
     */
    public function user_article(Request $request)
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $search = !empty($request->get('offset')) ? $request->get('offset') : null;
        $nbrPages = null;

        if(!empty($search)) {
            //
        } else {
            $articleLivingThing = $this->getDoctrine()->getRepository(ArticleLivingThing::class)->getArticleLivingThings($this->current_logged_user->getId(), $offset, $limit);
            $nbrPages = ceil($this->getDoctrine()->getRepository(ArticleLivingThing::class)->countArticleLivingThingsUser($this->current_logged_user->getId()) / $limit);
        }

        return $this->render('user/article/index.html.twig', [
            "articles" => $articleLivingThing,
            "offset" => $offset,
            "total_page" => $nbrPages
        ]);
    }

    /**
     * @Route("/user/article/add", name="userAddArticle")
     */
    public function user_add_article(Request $request, EntityManagerInterface $manager)
    {
        $article = new ArticleLivingThing();
        $formArticle = $this->createForm(ArticleLivingThingType::class, $article);
        $formArticle->handleRequest($request);

        if($formArticle->isSubmitted() && $formArticle->isValid()) {
            $this->articleLivingThingManager->insertArticleLivingThing(
                $formArticle, 
                $request, 
                $manager, 
                $this->getParameter('project_wikiearth_dir'), 
                $this->current_logged_user
            );
        }

        return $this->render('user/article/add.html.twig', [
            "formArticle" => $formArticle->createView()
        ]);
    }

    /**
     * @Route("/user/article/{id}/edit", name="userEditArticle")
     */
    public function user_edit_article(ArticleLivingThing $article, Request $request, EntityManagerInterface $manager)
    {
        $formArticle = $this->createForm(ArticleLivingThingType::class, $article);
        $formArticle->handleRequest($request);

        if($formArticle->isSubmitted() && $formArticle->isValid()) {
            $this->articleLivingThingManager->insertArticleLivingThing(
                $formArticle, 
                $request, 
                $manager, 
                $this->getParameter('project_wikiearth_dir'), 
                $this->current_logged_user
            );
        }

        return $this->render('user/article/add.html.twig', [
            "formArticle" => $formArticle->createView()
        ]);
    }

    /**
     * @Route("/user/notifications", name="userNotifs")
     */
    public function user_notifications()
    {
        return $this->render('user/notifications/index.html.twig', [
            "notifications" => []
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
