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
            "nbrAnimalia" => 10,
            "nbrPlantae" => 100,
            "nbrInsecta" => 1000,
            "nbrBacteria" => 10000,
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
            // dd($formUser->get("password")->getData(), $user);
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

        return $this->render('user/living_thing/index.html.twig', [
            "livingThings" => []
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
                $manager, 
                $this->getParameter('project_wikiearth_dir')
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
        $articleLivingThing = $this->getDoctrine()->getRepository(ArticleLivingThing::class)->getArticleLivingThingByLivingThingId($id);

        if(empty($articleLivingThing)) {
            $articleLivingThing = new ArticleLivingThing();
            $livingThing = $this->getDoctrine()->getRepository(LivingThing::class)->getLivingThingById($id);

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
                }
            } else {
                dd("L'identifiant {$id} n'existe pas.");
            }
        } else {
            dd("Il existe déjà un article sur cette être vivant.");
        }

        return $this->render('user/article/edit.html.twig', [
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
                $manager, 
                $this->getParameter('project_wikiearth_dir')
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

        if(!empty($search)) {
            //
        } else {
            $articleLivingThing = $this->current_logged_user->getArticleLivingThings();
        }

        return $this->render('user/article/index.html.twig', [
            "articles" => $articleLivingThing
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
     * @Route("/user/chat", name="userChat")
     */
    public function user_chat()
    {
        return $this->render('user/chat/index.html.twig');
    }
}
