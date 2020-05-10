<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserRegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $userRegister = new User();
        $formUserRegister = $this->createForm(UserRegisterType::class, $userRegister);
        $formUserRegister->handleRequest($request);

        if($formUserRegister->isSubmitted() && $formUserRegister->isValid()) {
            $userRegister->setPassword($encoder->encodePassword($userRegister, $userRegister->getPassword()));
            $userRegister->setRoles(['ROLE_USER']);
            $userRegister->setCreatedAt(new DateTime());
            $manager->persist($userRegister);
            $manager->flush();
        }

        return $this->render('anonymous/user/register.html.twig', [
            "userRegisterForm" => $formUserRegister->createView()
        ]);
    }
}
