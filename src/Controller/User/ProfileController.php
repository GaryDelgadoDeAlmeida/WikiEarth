<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Form\UserType;
use App\Manager\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/user", name="user")
 */
class ProfileController extends AbstractController
{
    private User $user;
    private UserManager $userManager;
    private EntityManagerInterface $em;
    
    public function __construct(
        Security $security,
        UserManager $userManager,
        EntityManagerInterface $em,
    ) {
        $this->user = $security->getUser();
        $this->em = $em;
        $this->userManager = $userManager;
    }
    /**
     * @Route("/profile", name="Profile")
     */
    public function user_profil(Request $request): Response
    {
        $form = $this->createForm(UserType::class, $this->user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            // Update personnal user data (lastname, firstname, img, password, etc ...)
            $this->userManager->updateUser(
                $form, 
                $this->user, 
                $this->getParameter('project_users_dir')
            );
        }
        
        return $this->render('user/profile/index.html.twig', [
            "user" => $this->user,
            "userImg" => $this->user->getImgPath() 
                ? $this->user->getImgPath() 
                : "https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1024px-User_icon_2.svg.png",
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/profile/delete", name="Delete")
     */
    public function user_delete_profil(Request $request)
    {
        foreach($user->getNotifications() as $oneNotification) {
            $this->em->remove($oneNotification);
        }

        $this->em->remove($this->currentLoggedUser);
        $this->em->flush();

        $this->redirectRoute("home");
    }
}
