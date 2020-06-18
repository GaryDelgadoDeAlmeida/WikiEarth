<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Article;
use App\Form\ArticleType;
use App\Entity\MediaGallery;
use App\Form\UserRegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AdminController extends AbstractController
{
    private $current_logged_user;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->current_logged_user = $tokenStorage->getToken()->getUser();
    }
    
    /**
     * @Route("/admin", name="adminHome")
     */
    public function admin_home()
    {
        return $this->render('admin/home/index.html.twig');
    }

    /**
     * @Route("/admin/profile", name="adminProfile")
     */
    public function admin_profile(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $formUser = $this->createForm(UserType::class, $this->current_logged_user);
        $formUser->handleRequest($request);

        if($formUser->isSubmitted() && $formUser->isValid()) {
            $manager->persist($this->current_logged_user);
            $manager->flush();
        }

        return $this->render('admin/profile/index.html.twig', [
            "userForm" => $formUser->createView()
        ]);
    }

    /**
     * @Route("/admin/users", name="adminUsersListing")
     */
    public function admin_users_listing(Request $request)
    {
        $limit = 15;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;

        // dd($this->getDoctrine()->getRepository(User::class)->getUsers($offset - 1, $limit, $this->current_logged_user->getId()));

        return $this->render('admin/users/index.html.twig', [
            "users" => $this->getDoctrine()->getRepository(User::class)->getUsers($offset - 1, $limit, $this->current_logged_user->getId()),
            "offset" => $offset,
            "total_page" => ceil($this->getDoctrine()->getRepository(User::class)->countUsers($this->current_logged_user->getId())["nbrUsers"] / $limit)
        ]);
    }

    /**
     * @Route("/admin/users/{id}", name="adminUserEdit")
     */
    public function admin_user_edit(User $user, Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $formUser = $this->createForm(UserType::class, $user);
        $formUser->handleRequest($request);

        if($formUser->isSubmitted() && $formUser->isValid()) {
            $manager->persist($user);
            $manager->flush();

            $this->redirectToRoute('adminUsersListing');
        }

        return $this->render('admin/users/edit.html.twig', [
            "userForm" => $formUser->createView()
        ]);
    }

    /**
     * @Route("/admin/users/{id}/delete", name="adminUserDelete")
     */
    public function admin_user_delete(User $user, EntityManagerInterface $manager)
    {
        $manager->remove($user);
        $manager->flush();

        return $this->redirectToRoute('adminUsersListing');
    }

    /**
     * @Route("/admin/article", name="adminArticle")
     */
    public function admin_article(Request $request)
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;

        return $this->render('admin/article/index.html.twig', [
            "articles" => $this->getDoctrine()->getRepository(Article::class)->getArticles($offset, $limit),
            "offset" => $offset,
            "total_page" => ceil($this->getDoctrine()->getRepository(Article::class)->countArticles()[1] / $limit)
        ]);
    }

    /**
     * @Route("/admin/article/add", name="adminAddArticle")
     */
    public function admin_add_article(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $article = new Article();
        $formArticle = $this->createForm(ArticleType::class, $article);
        $formArticle->handleRequest($request);

        if($formArticle->isSubmitted() && $formArticle->isValid()) {
            dd($article);
            // $manager->persist($article);
            // $manager->flush();

            // $this->redirectToRoute('adminUsersListing');
        }

        return $this->render('admin/article/edit.html.twig', [
            "formArticle" => $formArticle->createView()
        ]);
    }

    /**
     * @Route("/admin/media", name="adminMedia")
     */
    public function admin_media(Request $request)
    {
        return $this->render('admin/media/index.html.twig');
    }

    /**
     * @Route("/admin/media/{type}", name="adminMediaType")
     */
    public function admin_media_by_type($type, Request $request)
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;

        return $this->render('admin/media/list_media.html.twig', [
            "mediaType" => $type,
            "medias" => $this->getDoctrine()->getRepository(MediaGallery::class)->getMediaGalleryByType($type, $offset, $limit)
        ]);
    }
}
