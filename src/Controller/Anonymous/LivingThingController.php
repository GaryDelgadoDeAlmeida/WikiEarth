<?php

namespace App\Controller\Anonymous;

use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LivingThingController extends AbstractController
{
    private ArticleRepository $articleRepository;

    function __construct(ArticleRepository $articleRepository) {
        $this->articleRepository = $articleRepository;
    }

    /**
     * @Route("/living-thing/{name}", name="articleLivingThing")
     */
    public function article_living_thing(Request $request, string $name) : Response
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $kingdom = ucfirst($name);

        $livingThing = $this->articleRepository->getArticleLivingThingsByLivingThingKingdom($kingdom, $offset, $limit);
        $totalOffset = ceil($this->articleRepository->countArticleLivingThingsByKingdom($kingdom, $limit));

        return $this->render('anonymous/article/living-thing/list.html.twig', [
            "articles" => $livingThing,
            "name" => $name,
            "offset" => $offset,
            "nbrOffset" => $totalOffset
        ]);
    }

    /**
     * @Route("/living-thing/{name}/{id}", name="articleLivingThingByID")
     */
    public function article_living_thing_by_id(string $name, int $id) : Response
    {
        $articleLivingThing = [];
        $kingdom = ucfirst($name);

        $articleLivingThing = $this->articleRepository->getArticleLivingThingsByLivingThingKingdomByID($kingdom, $id);

        // S'il est vide (soit il n'existe pas, soit l'article n'est pas encore approuver) alors ...
        if(empty($articleLivingThing)) {
            return $this->redirectToRoute("articleLivingThing", [
                "name" => $name,
                "class" => "danger",
                "message" => "This living thing does not exist."
            ], 307);
        }

        // Article consulatation statistics
        $this->statisticsManager->updateArticlePageConsultationsStatistics();

        return $this->render('anonymous/article/living-thing/single.html.twig', [
            "article" => $articleLivingThing,
            "mediaGallery" => $articleLivingThing->getArticleLivingThing()->getMediaGallery(),
            "references" => [],
            "countries" => $articleLivingThing->getArticleLivingThing()->getLivingThing()->getCountries(),
            "name" => $name
        ]);
    }

    /**
     * @Route("/living-thing/{name}/{id}/pdf", name="articleLivingThingToPDF")
     */
    public function article_living_thing_to_pdf(string $name, int $id) : Response
    {
        $article = $this->articleRepository->getArticleLivingThingsByLivingThingKingdomByID($name, $id);

        if(empty($article)) {
            return false;
        }

        return $this->render("anonymous/article/living-thing/pdf.html.twig", [
            "article" => $article,
            "references" => $article->getArticleLivingThing()->getReference(),
        ]);
    }
}
