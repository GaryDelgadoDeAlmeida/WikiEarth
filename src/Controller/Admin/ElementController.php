<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Element;
use App\Form\ElementType;
use App\Manager\ElementManager;
use App\Repository\ElementRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin", name="admin")
 */
class ElementController extends AbstractController
{
    private User $user;
    private ElementManager $elementManager;
    private ElementRepository $elementRepository;
    
    public function __construct(
        Security $security,
        ElementManager $elementManager,
        ElementRepository $elementRepository
    ) {
        $this->user = $security->getUser();
        $this->elementManager = $elementManager;
        $this->elementRepository = $elementRepository;
    }

    /**
     * @Route("/element", name="Element")
     */
    public function admin_element(Request $request) : Response
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? \intval($request->get('offset')) : 1;
        $search = !empty($request->get('search')) ? $request->get('search') : null;
        $elements = [];
        $nbrElements = 0;

        if(!empty($search)) {
            $elements = $this->elementRepository->searchElements($search, $offset, $limit);
            $nbrElements = $this->elementRepository->countSearchElements($search);
        } else {
            $elements = $this->elementRepository->getElements($offset, $limit);
            $nbrElements = $this->elementRepository->countElements();
        }

        $nbrOffset = $nbrElements > $limit ? ceil($nbrElements / $limit) : 0;

        return $this->render('admin/article/natural-elements/listElement.html.twig', [
            "elements" => $elements,
            "offset" => $offset,
            "nbrOffset" => $nbrOffset,
            "search" => $search,
        ]);
    }

    /**
     * @Route("/element/add", name="AddElement")
     */
    public function admin_add_element(Request $request) : Response
    {
        $formElement = $this->createForm(ElementType::class, $element = new Element());
        $formElement->handleRequest($request);
        $response = [];

        if($formElement->isSubmitted() && $formElement->isValid()) {
            
            // On vérifie qu'il n'existe pas déjà un element du tableau périodique portant le même nom dans la base de données
            if(empty($this->elementRepository->getElementByScientificName($element->getScientificName()))) {
                $response = $this->elementManager->setElement(
                    $formElement["imgPath"]->getData(), 
                    $element,
                    $formElement,
                );
            } else {
                $response = [
                    "class" => "danger",
                    "message" => "The element {$element->getScientificName()} already exist in the databse."
                ];
            }
        }
        return $this->render('admin/article/natural-elements/formElement.html.twig', [
            "formElement" => $formElement->createView(),
            "response" => $response
        ]);
    }

    /**
     * @Route("/element/{id}/edit", name="EditElement")
     */
    public function admin_edit_element(Request $request, int $id) : Response
    {
        $element = $this->elementRepository->find($id);
        
        if(empty($element)) {
            throw new \Exception("This element hasn't been found");
        }

        $formElement = $this->createForm(ElementType::class, $element);
        $formElement->get("volumicMass")->setData(implode(" || ", $element->getVolumicMass()));
        $formElement->handleRequest($request);
        $response = [];

        if($formElement->isSubmitted() && $formElement->isValid()) {
            $response = $this->elementManager->setElement(
                $formElement["imgPath"]->getData(), 
                $element,
                $formElement
            );
        }
        return $this->render('admin/article/natural-elements/formElement.html.twig', [
            "formElement" => $formElement->createView(),
            "response" => $response
        ]);
    }

    /**
     * @Route("/element/{id}/delete", name="DeleteElement")
     */
    public function admin_delete_element(Request $request, int $id) : Response
    {
        $element = $this->elementRepository->find($id);
        
        if(empty($element)) {
            throw new \Exception("This element hasn't been found");
        }

        $this->elementRepository->remove($element, true);

        return $this->json([
            "error" => false,
            "class" => "success",
            "message" => "The element {$element->getName()} has been deleted"
        ]);
    }
}
