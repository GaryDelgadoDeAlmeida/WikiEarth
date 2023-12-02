<?php

namespace App\Controller\Anonymous;

use App\Entity\User;
use App\Form\UserLoginType;
use App\Form\UserRegisterType;
use App\Manager\ContactManager;
use App\Manager\StatisticsManager;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthentificationController extends AbstractController
{
    private EntityManagerInterface $em;
    private UserPasswordEncoderInterface $encoder;
    private ContactManager $contactManager;
    private StatisticsManager $statisticsManager;
    private UserRepository $userRepository;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $encoder,
        ContactManager $contactManager,
        StatisticsManager $statisticsManager,
        UserRepository $userRepository
    ) {
        $this->em = $em;
        $this->encoder = $encoder;
        $this->contactManager = $contactManager;
        $this->statisticsManager = $statisticsManager;
        $this->userRepository = $userRepository;
    }
    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils) : Response
    {
        // Retourne l'erreur d'authentification rencontrée
        $error = $authenticationUtils->getLastAuthenticationError();
        
        $formUserLogin = $this->createForm(UserLoginType::class, new User());
        $formUserLogin->handleRequest($request);

        // Manually authenticate user in controller
        // if($formUserLogin->isSubmitted() && $formUserLogin->isValid()) {
        //     $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        //     $this->get('security.token_storage')->setToken($token);
        //     $this->get('session')->set('_security_main', serialize($token));
        // }

        if(!empty($error)) {
            $error = [
                "class" => "danger",
                "content" => "The login or password isn't correct"
            ];
        }

        return $this->render('anonymous/user/login.html.twig', [
            "userLoginForm" => $formUserLogin->createView(),
            "error" => $error,
        ]);
    }

    /**
     * @Route("/login/check", name="checkUser")
     */
    public function checkUser(Security $security) : Response
    {
        // Authentified user connection statistics
        $this->statisticsManager->updateUserConnectionStatistics();

        $user = $security->getUser();
        if($user instanceof User) {
            if($user->hasRole("ROLE_ADMIN")) {
                return $this->redirectToRoute("adminHome");
            } elseif($user->hasRole("ROLE_USER")) {
                return $this->redirectToRoute("userHome");
            }
        }

        return $this->redirectToRoute("login");
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request) : Response
    {
        $formUserRegister = $this->createForm(UserRegisterType::class, $userRegister = new User());
        $formUserRegister->handleRequest($request);
        $response = [];

        if($formUserRegister->isSubmitted() && $formUserRegister->isValid()) {
            // If not occurence then there is no user with the chosen login
            if(empty($this->userRepository->getUserByLogin($userRegister->getLogin()))) {
                $userRegister->setPassword($this->encoder->encodePassword($userRegister, $userRegister->getPassword()));
                $userRegister->setRoles(['ROLE_USER']);
                $userRegister->setCreatedAt(new \DateTime());
                $this->em->persist($userRegister);
                $this->em->flush();

                // Mail sended to the admin informing a new user has arrived
                $this->contactManager->sendEmailToAdmin(
                    $userRegister->getEmail(), 
                    "A new WikiEarth user", 
                    "The user {$userRegister->getFirstname()} {$userRegister->getLastname()} ({$userRegister->getEmail()}) created an account on WikiEarth."
                );

                // Mail sended to the user welcoming him to the Wikiearth platerform
                $this->contactManager->sendEmailToUser(
                    $userRegister->getEmail(), 
                    "Welcome to WikiEarth", 
                    "You account {$userRegister->getLogin()} has been created.\n\n"
                );

                return $this->redirectToRoute('login');
            } else {
                $response = [
                    "class" => "warning",
                    "message" => "An user with the pseudo \"{$userRegister->getLogin()}\" already exist."
                ];
            }
        }

        return $this->render('anonymous/user/register.html.twig', [
            "userRegisterForm" => $formUserRegister->createView(),
            "response" => $response
        ]);
    }

    /**
     * @Route("/registerAdmin", name="adminRegister")
     */
    public function admin_register(Request $request) : Response
    {
        $formUserRegister = $this->createForm(UserRegisterType::class, $userRegister = new User());
        $formUserRegister->handleRequest($request);
        $response = [];

        if($formUserRegister->isSubmitted() && $formUserRegister->isValid()) {
            $userRegister->setPassword($this->encoder->encodePassword($userRegister, $userRegister->getPassword()));
            $userRegister->setRoles(['ROLE_ADMIN']);
            $userRegister->setCreatedAt(new \DateTime());
            $this->em->persist($userRegister);
            $this->em->flush();

            return $this->redirectToRoute('login');
        }

        return $this->render('anonymous/user/register.html.twig', [
            "userRegisterForm" => $formUserRegister->createView(),
            "response" => $response
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout() : Response
    {
        $this->redirectToRoute("home");
    }
}
