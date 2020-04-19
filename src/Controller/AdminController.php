<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="adminHome")
     */
    public function index()
    {
        return $this->render('admin/home/index.html.twig');
    }

    /**
     * @Route("/admin/logout", name="adminLogout")
     */
    public function admin_logout()
    {
        return $this->redirectToRoute('home');
    }
}
