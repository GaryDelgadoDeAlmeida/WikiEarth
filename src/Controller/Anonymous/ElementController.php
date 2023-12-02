<?php

namespace App\Controller\Anonymous;

use App\Manager\StatisticsManager;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ElementController extends AbstractController
{
    private StatisticsManager $statisticsManager;
    private ArticleRepository $articleRepository;
    
    function __construct(StatisticsManager $statisticsManager, ArticleRepository $articleRepository) {
        $this->statisticsManager = $statisticsManager;
        $this->articleRepository = $articleRepository;
    }

    /**
     * @Route("/element", name="articleElement")
     */
    public function article_element(Request $request) : Response
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $nbrOffset = ceil($this->articleRepository->countArticleElementsApproved() / $limit);

        return $this->render('anonymous/article/natural-elements/list.html.twig', [
            "elements" => $this->articleRepository->getArticleElementsApproved($offset, $limit),
            "offset" => $offset,
            "nbrOffset" => $nbrOffset,
        ]);
    }

    /**
     * @Route("/element/{id}", name="articleElementByID")
     */
    public function article_element_by_id(int $id) : Response
    {
        $element = $this->articleRepository->getArticleElement($id);
        if(empty($element)) {
            return $this->redirectToRoute('articleElement');
        }

        // Article consulatation statistics
        $this->statisticsManager->updateArticlePageConsultationsStatistics();

        return $this->render('anonymous/article/natural-elements/single.html.twig', [
            "article" => $element,
            "mediaGallery" => [],
            "references" => [],
        ]);
    }
}
