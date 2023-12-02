<?php

namespace App\Controller\Anonymous;

use App\Manager\StatisticsManager;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChimicalReactionController extends AbstractController
{
    private StatisticsManager $statisticsManager;
    private ArticleRepository $articleRepository;
    
    function __construct(StatisticsManager $statisticsManager, ArticleRepository $articleRepository) {
        $this->statisticsManager = $statisticsManager;
        $this->articleRepository = $articleRepository;
    }

    /**
     * @Route("/chimical-reaction", name="articleChimicalReaction")
     */
    public function article_chimical_reactions(Request $request) : Response
    {
        return $this->render("anonymous/article/chimical-reactions/list.html.twig", []);
    }
}
