<?php

namespace App\Controller;

use App\Entity\{User, LivingThing, Article, SourceLink, MediaGallery};
use App\Form\{UserType, LivingThingType, UserRegisterType, LivingThingArticleType};
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
     * @Route("/admin/living-thing", name="adminLivingThing")
     */
    public function admin_living_thing(Request $request)
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;

        return $this->render('admin/animal/index.html.twig', [
            "livingThings" => $this->getDoctrine()->getRepository(LivingThing::class)->getLivingThings($offset, $limit),
            "offset" => $offset,
            "total_page" => ceil($this->getDoctrine()->getRepository(Article::class)->countArticles()[1] / $limit)
        ]);
    }

    /**
     * @Route("/admin/living-thing/add", name="adminAddLivingThing")
     */
    public function admin_add_living_thing(Request $request, EntityManagerInterface $manager)
    {
        $livingThing = new LivingThing();
        $formLivingThing = $this->createForm(LivingThingType::class, $livingThing);
        $formLivingThing->handleRequest($request);

        if($formLivingThing->isSubmitted() && $formLivingThing->isValid()) {
            $mediaFile = $formLivingThing['imgPath']->getData();
            if($mediaFile) {
                $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $newFilename = $livingThing->getName().'.'.$mediaFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    if(
                        array_search(
                            $this->getParameter('project_wikiearth_dir').$livingThing->getKingdom()."/img/".$livingThing->getName()."/".$newFilename, 
                            glob($this->getParameter('project_wikiearth_dir').$livingThing->getKingdom()."/img/".$livingThing->getName()."/*.".$mediaFile->guessExtension())
                        )
                    ) {
                        unlink($this->getParameter('project_wikiearth_dir').$livingThing->getKingdom()."/img/".$livingThing->getName()."/".$newFilename);
                    }
                    
                    $mediaFile->move(
                        $this->getParameter('project_wikiearth_dir').$livingThing->getKingdom()."/img/".$livingThing->getName(),
                        $newFilename
                    );
                } catch (FileException $e) {
                    dd($e->getMessage());
                }

                $livingThing->setImgPath($this->getParameter('project_wikiearth_dir').$livingThing->getKingdom()."/img/".$livingThing->getName()."/".$newFilename);
            }

            $manager->persist($livingThing);
            $manager->flush();
        }

        return $this->render('admin/animal/edit.html.twig', [
            "formAnimal" => $formLivingThing->createView()
        ]);
    }

    /**
     * @Route("/admin/living-thing/{id}/edit", name="adminEditLivingThing")
     */
    public function admin_edit_living_thing(LivingThing $livingThing, Request $request, EntityManagerInterface $manager)
    {
        $formLivingThing = $this->createForm(LivingThingType::class, $livingThing);
        $formLivingThing->handleRequest($request);

        if($formLivingThing->isSubmitted() && $formLivingThing->isValid()) {
            $mediaFile = $formLivingThing['imgPath']->getData();
            if($mediaFile) {
                $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $newFilename = $livingThing->getName().'.'.$mediaFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    if(
                        array_search(
                            $this->getParameter('project_wikiearth_dir').$livingThing->getKingdom()."/img/".$livingThing->getName()."/".$newFilename, 
                            glob($this->getParameter('project_wikiearth_dir').$livingThing->getKingdom()."/img/".$livingThing->getName()."/*.".$mediaFile->guessExtension())
                        )
                    ) {
                        unlink($this->getParameter('project_wikiearth_dir').$livingThing->getKingdom()."/img/".$livingThing->getName()."/".$newFilename);
                    }
                    
                    $mediaFile->move(
                        $this->getParameter('project_wikiearth_dir').$livingThing->getKingdom()."/img/".$livingThing->getName(),
                        $newFilename
                    );
                } catch (FileException $e) {
                    dd($e->getMessage());
                }

                $livingThing->setImgPath($this->getParameter('project_wikiearth_dir').$livingThing->getKingdom()."/img/".$livingThing->getName()."/".$newFilename);
            }

            $manager->persist($livingThing);
            $manager->flush();
        }

        return $this->render('admin/animal/edit.html.twig', [
            "formAnimal" => $formLivingThing->createView()
        ]);
    }

    /**
     * @Route("/admin/living-thing/{id}/delete", name="adminDeleteLivingThing")
     */
    public function admin_delete_living_thing(LivingThing $livingThing, EntityManagerInterface $manager)
    {
        $manager->remove($livingThing);
        $manager->flush();

        return $this->redirectToRoute('adminLivingThing');
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
    public function admin_add_article(Request $request, EntityManagerInterface $manager)
    {
        $article = new Article();
        $formArticle = $this->createForm(LivingThingArticleType::class, $article);
        $formArticle->handleRequest($request);

        if($formArticle->isSubmitted() && $formArticle->isValid()) {
            
            dd($formArticle->getData(), $request->get('living_thing_article'), $formArticle['livingThingPhoto']->getData());
            
            $formRequest = $request->get('living_thing_article');
            
            $article = new Article();
            $article->setIdUser($this->get('security.token_storage')->getToken()->getId());
            $article->setTitle($formRequest['title']);
            $article->setCreatedAt(new \DateTime());

            $sourceLink_1 = new SourceLink();
            $sourceLink_1->setName($formRequest["postSourceLink_1"]['link']);
            $sourceLink_1->setLink($formRequest["postSourceLink_1"]['link']);
            $sourceLink_1->setIdArticle($article);

            $sourceLink_2 = new SourceLink();
            $sourceLink_2->setName($formRequest["postSourceLink_2"]['link']);
            $sourceLink_2->setLink($formRequest["postSourceLink_2"]['link']);
            $sourceLink_2->setIdArticle($article);

            $sourceLink_3 = new SourceLink();
            $sourceLink_3->setName($formRequest["postSourceLink_3"]['link']);
            $sourceLink_3->setLink($formRequest["postSourceLink_3"]['link']);
            $sourceLink_3->setIdArticle($article);

            $livingThing = new LivingThing();
            $livingThing->setCommonName($formRequest["livingThing"]['commonName']);
            $livingThing->setName($formRequest["livingThing"]['name']);
            $livingThing->setKingdom($formRequest["livingThing"]['kingdom']);
            $livingThing->setSubKingdom($formRequest["livingThing"]['subKingdom']);
            $livingThing->setDomain($formRequest["livingThing"]['domain']);
            $livingThing->setBranch($formRequest["livingThing"]['branch']);
            $livingThing->setSubBranch($formRequest["livingThing"]['subBranch']);
            $livingThing->setInfraBranch($formRequest["livingThing"]['infraBranch']);
            $livingThing->setDivision($formRequest["livingThing"]['division']);
            $livingThing->setSuperClass($formRequest["livingThing"]['superClass']);
            $livingThing->setClass($formRequest["livingThing"]['class']);
            $livingThing->setSubClass($formRequest["livingThing"]['subClass']);
            $livingThing->setInfraClass($formRequest["livingThing"]['infraClass']);
            $livingThing->setSuperOrder($formRequest["livingThing"]['superOrder']);
            $livingThing->setNormalOrder($formRequest["livingThing"]['normalOrder']);
            $livingThing->setSubOrder($formRequest["livingThing"]['subOrder']);
            $livingThing->setInfraOrder($formRequest["livingThing"]['infraOrder']);
            $livingThing->setMicroOrder($formRequest["livingThing"]['microOrder']);
            $livingThing->setSuperFamily($formRequest["livingThing"]['superFamily']);
            $livingThing->setFamily($formRequest["livingThing"]['family']);
            $livingThing->setSubFamily($formRequest["livingThing"]['subFamily']);
            $livingThing->setGenus($formRequest["livingThing"]['genus']);
            $livingThing->setSubGenus($formRequest["livingThing"]['subGenus']);
            $livingThing->setSpecies($formRequest["livingThing"]['species']);
            $livingThing->setSubSpecies($formRequest["livingThing"]['subSpecies']);

            $mediaFile = $formArticle['livingThingPhoto']->getData();
            // if($mediaFile) {
            //     $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);
            //     // this is needed to safely include the file name as part of the URL
            //     $newFilename = 'photo_garry_almeida.'.$mediaFile->guessExtension();

            //     // Move the file to the directory where brochures are stored
            //     try {
            //         if(array_search('./content/img/Photo/'.$newFilename, glob("./content/img/Photo/*.".$mediaFile->guessExtension()))) {
            //             unlink('./content/img/Photo/'.$newFilename);
            //         }
                    
            //         $mediaFile->move(
            //             $this->getParameter('photo_img_dir'),
            //             $newFilename
            //         );
            //     } catch (FileException $e) {
            //         dd($e->getMessage());
            //     }

            //     $mediaFile->setPath($newFilename);
            // }
            
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
