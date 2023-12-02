<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Manager\UserManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin", name="admin")
 */
class ProfileController extends AbstractController
{
    private User $user;
    private UserManager $userManager;

    function __construct(
        Security $security,
        UserManager $userManager
    ) {
        $this->user = $security->getUser();
        $this->userManager = $userManager;
    }
    
    /**
     * @Route("/profile", name="Profile")
     */
    public function admin_profile(Request $request)
    {
        $form = $this->createForm(UserType::class, $this->user);
        $form->handleRequest($request);
        $response = [];

        if($form->isSubmitted() && $form->isValid()) {
            $response = $this->userManager->updateUser(
                $form, 
                $this->user, 
                $this->getParameter('project_users_dir')
            );
        }

        return $this->render('admin/user/profile.html.twig', [
            "form" => $form->createView(),
            "userImg" => $this->user->getImgPath() ? $this->user->getImgPath() : "https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1024px-User_icon_2.svg.png",
            "response" => $response
        ]);
    }
}
