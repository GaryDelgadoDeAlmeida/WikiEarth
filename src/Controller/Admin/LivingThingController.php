<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\LivingThing;
use App\Form\LivingThingType;
use App\Entity\ArticleLivingThing;
use App\Manager\LivingThingManager;
use App\Form\ArticleLivingThingType;
use App\Repository\ArticleRepository;
use App\Repository\LivingThingRepository;
use App\Manager\ArticleLivingThingManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin", name="admin")
 */
class LivingThingController extends AbstractController
{
    private User $user;
    
    private LivingThingManager $livingThingManager;
    private ArticleLivingThingManager $articleLivingThingManager;
    
    private ArticleRepository $articleRepository;
    private LivingThingRepository $livingThingRepository;

    public function __construct(
        Security $security,
        LivingThingManager $livingThingManager,
        ArticleLivingThingManager $articleLivingThingManager,
        ArticleRepository $articleRepository,
        LivingThingRepository $livingThingRepository
    ) {
        $this->user = $security->getUser();
        $this->livingThingManager = $livingThingManager;
        $this->articleLivingThingManager = $articleLivingThingManager;
        $this->articleRepository = $articleRepository;
        $this->livingThingRepository = $livingThingRepository;
    }

    /**
     * @Route("/living-thing", name="LivingThing")
     */
    public function admin_living_thing(Request $request) : Response
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $search = !empty($request->get("search")) ? $request->get("search") : null;
        $nbrLivingThing = $livingThings = [];

        if(!empty($search)) {
            $livingThings = $this->livingThingRepository->searchLivingThing($search, $offset, $limit);
            $nbrLivingThing = $this->livingThingRepository->countSearchLivingThing($search);
        } else {
            $livingThings = $this->livingThingRepository->getLivingThings($offset, $limit);
            $nbrLivingThing = $this->livingThingRepository->countLivingThings();
        }

        $nbrOffset = $nbrLivingThing > $limit ? ceil($nbrLivingThing / $limit) : 1;

        return $this->render('admin/article/living-thing/listLivingThing.html.twig', [
            "livingThings" => $livingThings,
            "offset" => $offset,
            "nbrOffset" => $nbrOffset,
            "search" => $search,
        ]);
    }

    /**
     * @Route("/living-thing/add", name="AddLivingThing")
     */
    public function admin_add_living_thing(Request $request) : Response
    {
        $formLivingThing = $this->createForm(LivingThingType::class, $livingThing = new LivingThing());
        $formLivingThing->handleRequest($request);
        $message = [];

        if($formLivingThing->isSubmitted() && $formLivingThing->isValid()) {
            $message = $this->livingThingManager->setLivingThing(
                $formLivingThing["imgPath"]->getData(), 
                $livingThing, 
            );
        }

        return $this->render('admin/article/living-thing/formLivingThing.html.twig', [
            "formLivingThing" => $formLivingThing->createView(),
            "response" => $message
        ]);
    }

    /**
     * @Route("/living-thing/{id}/article", name="LivingThingCreateArticle")
     */
    public function admin_living_thing_create_article($id, Request $request) : Response
    {
        $articleLivingThing = $this->articleRepository->getArticleByLivingThing($id);
        $message = [];

        // If empty then there is no article so the user can create the article
        if(empty($articleLivingThing)) {
            $articleLivingThing = new ArticleLivingThing();
            $livingThing = $this->livingThingRepository->getLivingThing($id);

            if(!empty($livingThing)) {
                $formArticle = $this->createForm(ArticleLivingThingType::class, $articleLivingThing);
                $formArticle->get('livingThing')->setData($livingThing);
                $formArticle->handleRequest($request);

                if($formArticle->isSubmitted() && $formArticle->isValid()) {
                    $message = $this->articleLivingThingManager->setArticleLivingThing(
                        $articleLivingThing,
                        $livingThing,
                        $this->user
                    );
                }
            } else {
                return $this->redirectToRoute("adminLivingThing", [
                    "class" => "danger",
                    "message" => "This living thing does not exist"
                ], 307);
            }
        } else {
            return $this->redirectToRoute("adminLivingThing", [
                "class" => "danger",
                "message" => "This living thing already have an article"
            ], 307);
        }

        return $this->render('admin/article/living-thing/formArticle.html.twig', [
            "formArticle" => $formArticle->createView(),
            "response" => $message
        ]);
    }

    /**
     * @Route("/living-thing/{id}/edit", name="EditLivingThing")
     */
    public function admin_edit_living_thing(Request $request, LivingThing $livingThing) : Response
    {
        $formLivingThing = $this->createForm(LivingThingType::class, $livingThing);
        $formLivingThing->handleRequest($request);
        $message = [];

        if($formLivingThing->isSubmitted() && $formLivingThing->isValid()) {
            $message = $this->livingThingManager->setLivingThing(
                $formLivingThing["imgPath"]->getData(),
                $livingThing, 
            );
        }

        return $this->render('admin/article/living-thing/formLivingThing.html.twig', [
            "formLivingThing" => $formLivingThing->createView(),
            "response" => $message
        ]);
    }

    /**
     * Possibilité d'en faire une retour API
     * 
     * Attention : supprimer un living thing possèdant une liaison avec une autre table,
     * la donnée dans l'autre table et le living thing seront supprimés de la base de données.
     * 
     * @Route("/living-thing/{id}/delete", name="DeleteLivingThing")
     */
    public function admin_delete_living_thing(LivingThing $livingThing) : Response
    {
        if(!empty($livingThing->getImgPath())) {
            unlink($this->getParameter('project_public_dir') . $livingThing->getImgPath());
        }

        foreach($livingThing->getCountries() as $oneCountry) {
            $livingThing->removeCountry($oneCountry);
        }

        $this->livingThingRepository->remove($livingThing, true);

        return $this->redirectToRoute('adminLivingThing');
    }
}
