<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Entity\ArticleElement;
use App\Manager\ArticleManager;
use App\Manager\ContactManager;
use App\Manager\ElementManager;
use App\Form\ArticleElementType;
use App\Manager\StatisticsManager;
use App\Manager\NotificationManager;
use App\Repository\ElementRepository;
use App\Manager\ArticleElementManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/user", name="user")
 */
class ElementController extends AbstractController
{
    private User $user;

    private ArticleManager $articleManager;
    private ElementManager $elementManager;
    private ContactManager $contactManager;
    private StatisticsManager $statisticsManager;
    private NotificationManager $notificationManager;
    private ArticleElementManager $articleElementManager;
    
    private ElementRepository $elementRepository;

    public function __construct(
        Security $security,
        ArticleManager $articleManager,
        ElementManager $elementManager,
        ContactManager $contactManager,
        StatisticsManager $statisticsManager,
        NotificationManager $notificationManager,
        ArticleElementManager $articleElementManager,
        ElementRepository $elementRepository
    ) {
        $this->user = $security->getUser();
        $this->articleManager = $articleManager;
        $this->elementManager = $elementManager;
        $this->contactManager = $contactManager;
        $this->statisticsManager = $statisticsManager;
        $this->notificationManager = $notificationManager;
        $this->articleElementManager = $articleElementManager;
        $this->elementRepository = $elementRepository;
    }

    /**
     * @Route("/element", name="Element")
     */
    public function user_element(Request $request)
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $search = !empty($request->get("search")) ? $request->get("search") : null;
        $filterBy = !empty($request->get("filter-by-element")) ? $request->get("filter-by-element") : "all";
        $filterChoices = [
            "all" => "All",
            "have-article" => "Have an article",
            "not-have-article" => "Not have an article"
        ];
        $elements = [];
        $nbrPages = 1;
        
        if(empty($search)) {
            if($filterBy != "all" && array_key_exists($filterBy, $filterChoices)) {
                if($filterBy == "have-article") {
                    $elements = $this->elementRepository->getElementsWithArticle($offset, $limit);
                    $nbrPages = ceil($this->elementRepository->countElementsWithArticle() / $limit);
                } elseif($filterBy == "not-have-article") {
                    $elements = $this->elementRepository->getElementsWithoutArticle($offset, $limit);
                    $nbrPages = ceil($this->elementRepository->countElementsWithoutArticle() / $limit);
                }
            } else {
                $elements = $this->elementRepository->getElements($offset, $limit);
                $nbrPages = ceil($this->elementRepository->countElements() / $limit);
            }
        } else {
            $filterBy = "all";
            $elements = $this->elementRepository->searchElements($search, $offset, $limit);
            $nbrPages = ceil($this->elementRepository->countSearchElements($search) / $limit);
        }
        
        return $this->render('user/article/elements/listElement.html.twig', [
            "filterChoices" => $filterChoices,
            "filter_by" => $filterBy,
            "elements" => $elements,
            "search" => $search,
            "offset" => $offset,
            "total_page" => $nbrPages,
        ]);
    }

    /**
     * @Route("/element/{id}/article", name="ElementCreateArticle")
     */
    public function user_element_create_article(int $id, Request $request)
    {
        $element = $this->elementRepository->find($id);
        $response = [];
        
        if(empty($element)) {
            return $this->redirectToRoute("userElement", [
                "error" => true,
                "class" => "danger",
                "message" => "The element you tryied to access hasn't been found."
            ]);
        }

        if(!empty($element->getArticleElement())) {
            return $this->redirectToRoute("userElement", [
                "error" => true,
                "class" => "danger",
                "message" => "The element {$element->getName()} already have an article."
            ]);
        }

        $formArticle = $this->createForm(ArticleElementType::class, $articleElement = new ArticleElement());
        $formArticle->get("element")->setData($element);
        $formArticle->get("element")->get("volumicMass")->setData(implode(" || ", $element->getVolumicMass()));
        $formArticle->handleRequest($request);

        if($formArticle->isSubmitted() && $formArticle->isValid()) {
            $response = $this->elementManager->setElement(
                $formArticle["element"]["imgPath"]->getData(),
                $element,
                $formArticle["element"],
                $this->manager
            );

            if(!empty($response) && $response["error"] == false) {
                $response = $this->articleElementManager->setArticleElement(
                    $articleElement,
                    $element,
                    $this->manager
                );

                // Insert reference of the content of the article
                // $response = $this->referenceManager->setReferences(
                //     $formArticle["references"]->getData(),
                //     $articleElementManager,
                //     $this->manager
                // );

                // Working logic of new table 
                $response = $this->articleManager->insertArticle(
                    $articleElement, 
                    $this->manager, 
                    $this->user
                );

                // We send a notification to the user
                $this->notificationManager->userCreateArticle($this->user);

                // Send a notification email to admin
                $this->contactManager->sendEmailToAdmin($this->getParameter("admin_email"), "New article {$element->getName()}", "A new article has been created. Please, go to the back office to approuve or delete the article.");

                // Article creation statistics
                $this->statisticsManager->updateArticleCreationsStatistics();

                return $this->redirectToRoute("userElement", [
                    "response" => $response
                ], 302);
            }
        }

        return $this->render('user/article/elements/formArticle.html.twig', [
            "formArticle" => $formArticle->createView(),
            "response" => $response
        ]);
    }
}
