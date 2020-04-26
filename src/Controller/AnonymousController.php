<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AnonymousController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('anonymous/home/index.html.twig');
    }

    /**
     * @Route("/about", name="about")
     */
    public function about()
    {
        return $this->render('anonymous/about/index.html.twig');
    }
}
