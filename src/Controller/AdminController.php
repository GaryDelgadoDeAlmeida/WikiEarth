<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\SourceLink;
use App\Entity\LivingThing;
use App\Entity\MediaGallery;
use App\Manager\UserManager;
use App\Form\LivingThingType;
use App\Form\UserRegisterType;
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

class AdminController extends AbstractController
{
    private $current_logged_user;
    private $em;
    private $livingThingManager;
    private $articleLivingThingManager;
    private $userManager;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $em)
    {
        $this->current_logged_user = $tokenStorage->getToken()->getUser();
        $this->em = $em;
        $this->livingThingManager = new LivingThingManager();
        $this->articleLivingThingManager = new ArticleLivingThingManager();
        $this->userManager = new UserManager();
    }
    
    /**
     * @Route("/admin", name="adminHome")
     */
    public function admin_home()
    {
        return $this->render('admin/home/index.html.twig', [
            "nbrUsers" => $this->em->getRepository(User::class)->countUsers($this->current_logged_user->getId()),
            "nbrArticles" => $this->em->getRepository(ArticleLivingThing::class)->countArticleLivingThings(),
            "nbrLivingThings" => $this->em->getRepository(LivingThing::class)->countLivingThings()
        ]);
    }

    /**
     * @Route("/admin/profile", name="adminProfile")
     */
    public function admin_profile(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $formUser = $this->createForm(UserType::class, $this->current_logged_user);
        $formUser->handleRequest($request);

        if($formUser->isSubmitted() && $formUser->isValid()) {
            $this->userManager->updateUser(
                $formUser, 
                $this->current_logged_user, 
                $this->em, 
                $encoder, 
                $this->getParameter('project_users_dir')
            );
        }

        return $this->render('admin/profile/index.html.twig', [
            "userForm" => $formUser->createView(),
            "userImg" => $this->current_logged_user->getImgPath() ? $this->current_logged_user->getImgPath() : "https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1024px-User_icon_2.svg.png"
        ]);
    }

    /**
     * @Route("/admin/users", name="adminUsersListing")
     */
    public function admin_users_listing(Request $request)
    {
        $limit = 15;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;

        return $this->render('admin/users/index.html.twig', [
            "users" => $this->em->getRepository(User::class)->getUsers($offset - 1, $limit, $this->current_logged_user->getId()),
            "offset" => $offset,
            "total_page" => ceil($this->em->getRepository(User::class)->countUsers($this->current_logged_user->getId()) / $limit)
        ]);
    }

    /**
     * @Route("/admin/users/{id}", name="adminUserEdit")
     */
    public function admin_user_edit(User $user, Request $request, UserPasswordEncoderInterface $encoder)
    {
        $formUser = $this->createForm(UserType::class, $user);
        $formUser->handleRequest($request);

        if($formUser->isSubmitted() && $formUser->isValid()) {
            $this->userManager->updateUser(
                $formUser, 
                $user, 
                $this->em, 
                $encoder, 
                $this->getParameter('project_users_dir')
            );

            $this->redirectToRoute('adminUsersListing');
        }

        return $this->render('admin/users/edit.html.twig', [
            "userForm" => $formUser->createView(),
            "userImg" => $user->getImgPath() ? $user->getImgPath() : "https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1024px-User_icon_2.svg.png"
        ]);
    }
    
    /**
     * @Route("/admin/users/{id}/delete", name="adminUserDelete")
     */
    public function admin_user_delete(User $user)
    {
        $this->em->remove($user);
        $this->em->flush();

        return $this->redirectToRoute('adminUsersListing');
    }

    /**
     * @Route("/admin/living-thing", name="adminLivingThing")
     */
    public function admin_living_thing(Request $request)
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;

        return $this->render('admin/living_thing/index.html.twig', [
            "livingThings" => $this->em->getRepository(LivingThing::class)->getLivingThings($offset, $limit),
            "offset" => $offset,
            "nbrOffset" => ceil($this->em->getRepository(LivingThing::class)->countLivingThings() / $limit)
        ]);
    }

    /**
     * @Route("/admin/living-thing/add", name="adminAddLivingThing")
     */
    public function admin_add_living_thing(Request $request)
    {
        $livingThing = new LivingThing();
        $formLivingThing = $this->createForm(LivingThingType::class, $livingThing);
        $formLivingThing->handleRequest($request);

        if($formLivingThing->isSubmitted() && $formLivingThing->isValid()) {
            $this->livingThingManager->setLivingThing(
                $formLivingThing["imgPath"]->getData(), 
                $livingThing, 
                $this->em
            );
        }

        return $this->render('admin/living_thing/edit.html.twig', [
            "formLivingThing" => $formLivingThing->createView()
        ]);
    }

    /**
     * @Route("/admin/living-thing/{id}/article", name="adminLivingThingCreateArticle")
     */
    public function admin_living_thing_create_article($id, Request $request)
    {
        $articleLivingThing = $this->em->getRepository(ArticleLivingThing::class)->findOneBy(["id" => $id]);

        if(empty($articleLivingThing)) {
            $articleLivingThing = new ArticleLivingThing();
            $livingThing = $this->em->getRepository(LivingThing::class)->getLivingThing($id);

            if(!empty($livingThing)) {
                $formArticle = $this->createForm(ArticleLivingThingType::class, $articleLivingThing);
                $formArticle->get('livingThing')->setData($livingThing);
                $formArticle->handleRequest($request);

                if($formArticle->isSubmitted() && $formArticle->isValid()) {
                    $this->articleLivingThingManager->setArticleLivingThing(
                        $articleLivingThing,
                        $livingThing,
                        $this->em,
                        $this->current_logged_user
                    );
                }
            } else {
                return $this->redirectToRoute("404Error");
            }
        } else {
            // dd("Il existe déjà un article sur cette être vivant.");
            return $this->redirectToRoute("404Error");
        }

        return $this->render('admin/article/living-thing/new.html.twig', [
            "formArticle" => $formArticle->createView()
        ]);
    }

    /**
     * @Route("/admin/living-thing/{id}/edit", name="adminEditLivingThing")
     */
    public function admin_edit_living_thing(LivingThing $livingThing, Request $request)
    {
        $formLivingThing = $this->createForm(LivingThingType::class, $livingThing);
        $formLivingThing->handleRequest($request);

        if($formLivingThing->isSubmitted() && $formLivingThing->isValid()) {
            $this->livingThingManager->setLivingThing(
                $formLivingThing, 
                $livingThing, 
                $this->em
            );
        }

        return $this->render('admin/living_thing/edit.html.twig', [
            "formLivingThing" => $formLivingThing->createView()
        ]);
    }

    /**
     * Possibilité d'en faire une response API
     * 
     * Attention : supprimer un living thing possèdant une liaison avec une autre table,
     * la donnée dans l'autre table et le living thing seront supprimés de la base de données.
     * 
     * @Route("/admin/living-thing/{id}/delete", name="adminDeleteLivingThing")
     */
    public function admin_delete_living_thing(LivingThing $livingThing)
    {
        $imgPath = $this->getParameter('project_public_dir') . $livingThing->getImgPath();
        foreach($livingThing->getCountries() as $oneCountry) {
            $livingThing->removeCountry($oneCountry);
        }
        unset($imgPath);
        $this->em->remove($livingThing);
        $this->em->flush();

        return $this->redirectToRoute('adminLivingThing');
    }

    /**
     * @Route("/admin/article", name="adminArticle")
     */
    public function admin_article(Request $request)
    {
        return $this->render('admin/article/index.html.twig');
    }

    /**
     * Affiche les articles selon la categorie d'appartenance. C'est-à-dire, on affiche 
     * les articles sur les êtres vivants si c'est la categorie demandée est les êtres vivants
     * 
     * @Route("/admin/article/{category}", name="adminArticleByCategory")
     */
    public function admin_article_by_category(string $category, Request $request)
    {
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $limit = 10;

        if($category == "living-thing") {
            return $this->render('admin/article/living-thing/index.html.twig', [
                "articles" => $this->em->getRepository(ArticleLivingThing::class)->getArticleLivingThings($offset, $limit),
                "nbrOffset" => ceil($this->em->getRepository(ArticleLivingThing::class)->countArticleLivingThings() / $limit),
                "offset" => $offset,
                "category" => $category
            ]);
        } elseif($category == "natural-elements") {
            die("Cette partie n'est pas encore disponible.");
        }

        return $this->redirectToRoute("404Error");
    }

    /**
     * Ajout un article celon le type (la categorie => "living-thing" ou "natural-elements") de l'article.
     * 
     * @Route("/admin/article/{category}/add", name="adminAddArticleByCategory")
     */
    public function admin_add_article_by_category(string $category, Request $request)
    {
        if($category == "living-thing") {
            $article = new ArticleLivingThing();
            $formArticle = $this->createForm(ArticleLivingThingType::class, $article);
            $formArticle->handleRequest($request);

            // Quand le formulaire est soumit et valide celon la config dans l'entity
            if($formArticle->isSubmitted() && $formArticle->isValid()) {
                $livingThing = $this->livingThingManager->setLivingThing(
                    $formArticle["livingThing"]["imgPath"]->getData(),
                    $formArticle["livingThing"]->getData(),
                    $this->em
                );

                $this->articleLivingThingManager->setArticleLivingThing(
                    $article,
                    $livingThing,
                    $this->em,
                    $this->current_logged_user
                );
            }

            return $this->render('admin/article/living-thing/edit.html.twig', [
                "formArticle" => $formArticle->createView(),
                "category" => $category
            ]);
        } elseif($category == "natural-elements") {
            die("Cette partie n'est pas encore disponible.");
        }

        return $this->redirectToRoute("404Error");
    }

    /**
     * @Route("/admin/article/{category}/{id}", name="adminSingleArticleByCategory")
     */
    public function admin_single_article_by_category(int $id, string $category)
    {
        if($category == "living-thing") {
            return $this->render('admin/article/living-thing/details.html.twig', [
                "article" => $this->em->getRepository(ArticleLivingThing::class)->findOneBy(["id" => $id]),
                "category" => $category
            ]);
        } elseif ($category == "natural-elements") {
            die("Cette partie n'est pas encore disponible.");
        }

        return $this->redirectToRoute("404Error");
    }

    /**
     * @Route("/admin/article/{category}/{id}/approve", name="adminApproveArticleByCategory")
     */
    public function admin_approve_single_article_by_category(int $id, string $category)
    {
        $article = null;
        if($category == "living-thing") {
            $article = $this->em->getRepository(ArticleLivingThing::class)->findOneBy(["id" => $id]);
        } elseif ($category == "natural-elements") {
            die("Cette partie n'est pas encore disponible.");
        }

        if(empty($article)) {
            return $this->redirectToRoute("404Error");
        }

        if(!$article->getApproved()) {
            $notfication = new Notification();
            $notfication->setUser($article->getUser());
            $notfication->setType("success");
            $notfication->setContent("The content of the article {$article->getTitle()} you writed is accurate. This article is now public.");
            $notfication->setCreatedAt(current_time("mysql"));
            $article->setApproved(true);
            $this->em->persist($article);
            $this->em->persist($notfication);
            $this->em->flush();
        }

        return $this->redirectToRoute("adminArticleByCategory", [
            "category" => $category
        ]);
    }

    /**
     * @Route("/admin/article/{category}/{id}/edit", name="adminEditArticleByCategory")
     */
    public function admin_edit_article_by_category(int $id, string $category, Request $request)
    {
        if($category == "living-thing") {
            $articleLivingThing = $this->em->getRepository(ArticleLivingThing::class)->findOneBy(["id" => $id]);
            
            if(empty($articleLivingThing)) {
                return $this->redirectToRoute("404Error");
            }
            
            $formArticle = $this->createForm(ArticleLivingThingType::class, $articleLivingThing);
            $formArticle->get('livingThing')->setData($articleLivingThing->getIdLivingThing());
            $formArticle->handleRequest($request);

            if($formArticle->isSubmitted() && $formArticle->isValid()) {
                $this->articleLivingThingManager->setArticleLivingThing(
                    $articleLivingThing,
                    $articleLivingThing->getIdLivingThing(),
                    $this->em
                );
            }

            return $this->render('admin/article/living-thing/edit.html.twig', [
                "formArticle" => $formArticle->createView(),
                "category" => $category
            ]);
        } elseif($category == "natural-elements") {
            die("Cette partie n'est pas encore disponible.");
        }

        return $this->redirectToRoute("404Error");
    }

    /**
     * Possibilité d'en faire une response API
     * 
     * Supprimer un article uniquement. La liaison 1-1 avec un living thing que l'article possède
     * ne sera pas affectée
     * 
     * @Route("/admin/article/{category}/{id}/delete", name="adminDeleteArticleByCategory")
     */
    public function admin_delete_article_by_category(int $id, string $category)
    {
        $article = null;
        if($category == "living-thing") {
            $article = $this->em->getRepository(ArticleLivingThing::class)->findOneBy(["id" => $id]);
        } elseif($category == "natural-elements") {
            die("Cette partie n'est pas encore disponible.");
        }

        if(empty($article)) {
            return $this->redirectToRoute("404Error");
        }

        $article->setIdLivingThing(null);
        $notfication = new Notification();
        $notfication->setUser($article->getUser());
        $notfication->setType("danger");
        $notfication->setContent("The content of the article {$article->getTitle()} you writed wasn't accurate. This article has been rejected.");
        $notfication->setCreatedAt(current_time("mysql"));
        $this->em->remove($article);
        $this->em->persist($notfication);
        $this->em->flush();

        return $this->redirectToRoute('adminArticleByCategory', [
            "category" => $category
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
            "medias" => $this->em->getRepository(MediaGallery::class)->getMediaGalleryByType($type, $offset, $limit)
        ]);
    }
}
