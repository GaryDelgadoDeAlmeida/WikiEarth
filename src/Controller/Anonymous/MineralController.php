<?php

namespace App\Controller\Anonymous;

use App\Manager\StatisticsManager;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MineralController extends AbstractController
{
    private StatisticsManager $statisticsManager;
    private ArticleRepository $articleRepository;
    
    function __construct(StatisticsManager $statisticsManager, ArticleRepository $articleRepository) {
        $this->statisticsManager = $statisticsManager;
        $this->articleRepository = $articleRepository;
    }

    /**
     * @Route("/mineral", name="articleMineral")
     */
    public function article_mineral(Request $request) : Response
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $nbrOffset = ceil($this->articleRepository->countArticleMineralsApproved() / $limit);

        return $this->render('anonymous/article/minerals/list.html.twig', [
            "minerals" => $this->articleRepository->getArticleMineralsApproved($offset, $limit),
            "offset" => $offset,
            "nbrOffset" => $nbrOffset,
        ]);
    }

    /**
     * @Route("/mineral/{id}", name="articleMineralByID")
     */
    public function article_mineral_by_id(int $id) : Response
    {
        $article = $this->articleRepository->getArticleMineral($id);
        if(empty($article)) {
            return $this->redirectToRoute('404Error');
        }

        // Article consulatation statistics
        $this->statisticsManager->updateArticlePageConsultationsStatistics();
        
        return $this->render('anonymous/article/minerals/single.html.twig', [
            "article" => $article,
            "mediaGallery" => [],
            "references" => [],
            "countries" => $article->getArticleMineral()->getMineral()->getCountry()
        ]);
    }
}
