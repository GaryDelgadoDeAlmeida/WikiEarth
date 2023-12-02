<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Mineral;
use App\Form\MineralType;
use App\Manager\MineralManager;
use App\Repository\MineralRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin", name="admin")
 */
class MineralController extends AbstractController
{
    private User $user;
    private MineralManager $mineralManager;
    private MineralRepository $mineralRepository;

    function __construct(
        Security $security,
        MineralManager $mineralManager,
        MineralRepository $mineralRepository
    ) {
        $this->user = $security->getUser();
        $this->mineralManager = $mineralManager;
        $this->mineralRepository = $mineralRepository;
    }

    /**
     * @Route("/mineral", name="Mineral")
     */
    public function admin_mineral(Request $request) : Response
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $search = !empty($request->get('search')) ? $request->get('search') : null;
        $minerals = [];
        $nbrPages = 1;

        if(!empty($search)) {
            $minerals = $this->mineralRepository->searchMineral($search, $offset, $limit);
            $nbrPages = ceil($this->mineralRepository->countSearchMineral($search) / $limit);
        } else {
            $minerals = $this->mineralRepository->getMinerals($offset, $limit);
            $nbrPages = ceil($this->mineralRepository->countMinerals() / $limit);
        }

        return $this->render('admin/article/minerals/listMineral.html.twig', [
            "offset" => $offset,
            "nbrOffset" => $nbrPages,
            "minerals" => $minerals,
            "search" => $search,
        ]);
    }

    /**
     * @Route("/mineral/add", name="AddMineral")
     */
    public function admin_add_mineral(Request $request) : Response
    {
        $formMineral = $this->createForm(MineralType::class, $mineral = new Mineral());
        $formMineral->handleRequest($request);
        $response = [];

        if($formMineral->isSubmitted() && $formMineral->isValid()) {
            // On vérifie qu'il n'existe pas déjà un mineral portant le même nom dans la base de données
            if(empty($this->mineralRepository->getMineralByName($mineral->getName()))) {
                $response = $this->mineralManager->setMineral(
                    $formMineral["imgPath"]->getData(), 
                    $mineral,
                    $formMineral
                );
            } else {
                $response = [
                    "class" => "danger",
                    "message" => "The mineral {$mineral->getName()} already exist in the databse."
                ];
            }
        }
        return $this->render('admin/article/minerals/formMineral.html.twig', [
            "formMineral" => $formMineral->createView(),
            "response" => $response
        ]);
    }

    /**
     * @Route("/mineral/{id}/edit", name="EditMineral")
     */
    public function admin_mineral_edit_by_id(Request $request, int $id) : Response
    {
        $mineral = $this->mineralRepository->findOneBy(["id" => $id]);

        if(empty($mineral)) {
            throw new \Exception("The mineral with the id {$id} wasn't found.");
        }

        $formMineral = $this->createForm(MineralType::class, $mineral);
        $formMineral->get('imaStatus')->setData(implode(", ", $mineral->getImaStatus()));
        $formMineral->handleRequest($request);
        $response = [];

        if($formMineral->isSubmitted() && $formMineral->isValid()) {
            $response = $this->mineralManager->setMineral(
                $formMineral["imgPath"]->getData(), 
                $mineral,
                $formMineral
            );
        }

        return $this->render('admin/article/minerals/formMineral.html.twig', [
            "formMineral" => $formMineral->createView(),
            "response" => $response
        ]);
    }

    /**
     * Possibilité d'en faire une retour API
     * 
     * Attention : supprimer un minéral possèdant une liaison avec une autre table,
     * la donnée dans l'autre table et le minéral seront supprimés de la base de données.
     * 
     * @Route("/mineral/{id}/delete", name="DeleteMineral")
     */
    public function admin_mineral_delete_by_id(Request $request, int $id) : Response
    {
        $mineral = $this->mineralRepository->findOneBy(["id" => $id]);

        if(empty($mineral)) {
            throw new \Exception("The mineral with the id {$id} wasn't found.");
        }

        // Si le mineral possède une image
        if(!empty($mineral->getImgPath())) {
            unlink($this->getParameter('project_public_dir') . $mineral->getImgPath());
        }

        foreach($mineral->getCountries() as $oneCountry) {
            $mineral->removeCountry($oneCountry);
        }

        $this->mineralRepository->remove($mineral, true);

        return $this->redirectToRoute('adminLivingThing');
    }
}
