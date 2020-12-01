<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
{
    /**
     * @Route("/403", name="403Error")
     */
    public function error403()
    {
        return $this->render('error/403.html.twig');
    }

    /**
     * @Route("/404", name="404Error")
     */
    public function error404()
    {
        return $this->render('error/404.html.twig');
    }

    /**
     * @Route("/500", name="500Error")
     */
    public function error500()
    {
        return $this->render('error/500.html.twig');
    }
}
