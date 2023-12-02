<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Manager\UserManager;
use App\Form\UserRegisterType;
use App\Repository\UserRepository;
use App\Repository\NotificationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin", name="admin")
 */
class UserController extends AbstractController
{
    private User $user;
    private UserPasswordEncoderInterface $encoder;
    private UserManager $userManager;
    private UserRepository $userRepository;
    private NotificationRepository $notificationRepository;

    public function __construct(
        Security $security,
        UserPasswordEncoderInterface $encoder,
        UserManager $userManager,
        UserRepository $userRepository,
        NotificationRepository $notificationRepository
    ) {
        $this->user = $security->getUser();
        $this->encoder = $encoder;
        $this->userManager = $userManager;
        $this->userRepository = $userRepository;
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @Route("/users", name="UsersListing")
     */
    public function admin_users_listing(Request $request) : Response
    {
        $limit = 15;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $nbrUsers = $this->userRepository->countUsers($this->user->getId());
        $nbrOffset = $nbrUsers > $limit ? ceil($nbrUsers / $limit) : 1;

        return $this->render('admin/user/listUsers.html.twig', [
            "users" => $this->userRepository->getUsers($offset - 1, $limit, $this->user->getId()),
            "offset" => $offset,
            "total_page" => $nbrOffset
        ]);
    }

    /**
     * @Route("/users/add", name="UserAdd")
     */
    public function admin_user_add(Request $request) : Response
    {
        $response = [];
        $form = $this->createForm(UserRegisterType::class, $user = new User());
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            try {
                if(empty($this->userRepository->getUserByLogin(trim($user->getLogin())))) {
                    if(trim($user->getPassword()) == trim($form["confirmPassword"]->getData())) {
                        $user->setLogin(trim($user->getLogin()));
                        $user->setPassword($this->encoder->encodePassword($user, trim($user->getPassword())));
                        $user->setCreatedAt(new \DateTime());
                        $this->em->persist($user);
                        $this->em->flush();

                        // $this->contactManager->sendEmailToUser(
                        //     $user->getEmail(),
                        //     "Welcome to WikiEarth",
                        //     "Welcome {$user->getFirstname()} {$user->getLastname()}."
                        // );
    
                        $response = [
                            "class" => "success",
                            "message" => "The user {$user->getLogin()} has been successfully created."
                        ];
                    } else {
                        $response = [
                            "class" => "warning",
                            "message" => "The password isn't the same. Please, check it."
                        ];
                    }
                } else {
                    $response = [
                        "class" => "danger",
                        "message" => "The username {$user->getLogin()} is already in use. Please, choose a different username."
                    ];
                }
            } catch(\Exception $e) {
                $response = [
                    "class" => "danger",
                    "message" => $e->getMessage()
                ];
            } finally {}
        }

        return $this->render("admin/user/formUser.html.twig", [
            "form" => $form->createView(),
            "response" => $response
        ]);
    }

    /**
     * @Route("/users/{id}", name="UserEdit")
     */
    public function admin_user_edit(Request $request, User $user) : Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        $response = [];

        if($form->isSubmitted() && $form->isValid()) {
            $response = $this->userManager->updateUser(
                $form, 
                $user, 
                $this->getParameter('project_users_dir')
            );

            $this->redirectToRoute('adminUsersListing');
        }

        return $this->render('admin/user/profile.html.twig', [
            "form" => $form->createView(),
            "userImg" => $user->getImgPath() 
                ? $user->getImgPath() 
                : "https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1024px-User_icon_2.svg.png",
            "response" => $response
        ]);
    }
    
    /**
     * @Route("/users/{id}/delete", name="UserDelete")
     */
    public function admin_user_delete(User $user) : Response
    {
        foreach($user->getNotifications() as $notification) {
            $this->notificationRepository->remove($notification, true);
        }
        
        $this->userRepository->remove($user, true);

        return $this->redirectToRoute('adminUsersListing', [
            "response" => [
                "class" => "success",
                "content" => "L'utilisateur {$user->getFirstname()} {$user->getLastname()} a bien été supprimé."
            ]
        ], 302);
    }
}
