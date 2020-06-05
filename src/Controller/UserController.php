<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="userHome")
     */
    public function user_home()
    {
        return $this->render('user/home/index.html.twig');
    }

    /**
     * @Route("/user/article", name="userArticle")
     */
    public function user_article()
    {
        return $this->render('user/article/index.html.twig');
    }

    /**
     * @Route("/user/article/add", name="userArticleAdd")
     */
    public function user_article_add()
    {
        return $this->render('user/article/add.html.twig');
    }
}
