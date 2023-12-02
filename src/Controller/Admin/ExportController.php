<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin", name="admin")
 */
class ExportController extends AbstractController
{
    private User $user;

    function __construct(
        Security $security
    ) {
        $this->user = $security->getUser();
    }

    /**
     * @Route("/exports", name="Exports")
     */
    public function admin_exports(Request $request) : Response
    {
        return $this->render("admin/exports/index.html.twig", []);
    }
}
