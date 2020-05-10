<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
     * @Route("/admin/profile", name="adminProfile")
     */
    public function admin_profile()
    {
        return $this->render('admin/profile/index.html.twig');
    }

    /**
     * @Route("/admin/users", name="adminListingUsers")
     */
    public function admin_listing_users(Request $request)
    {
        $limit = 15;
        $offset = preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;

        return $this->render('admin/users/index.html.twig', [
            "listingUsers" => $this->getDoctrine()->getRepository(User::class)->getUsers($offset - 1, $limit),
            "offset" => $offset,
            "total_page" => ceil($this->getDoctrine()->getRepository(User::class)->countUsers() / $limit)
        ]);
    }

    /**
     * @Route("/admin/logout", name="adminLogout")
     */
    public function admin_logout()
    {
        return $this->redirectToRoute('home');
    }
}
