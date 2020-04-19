<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="userHome")
     */
    public function index()
    {
        return $this->render('user/home/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
