<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Country;
use App\Form\UserLoginType;
use App\Form\UserRegisterType;
use App\Entity\ArticleLivingThing;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AnonymousController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        $countrys = $this->getDoctrine()->getRepository(Country::class)->findAll();
        $nbrCountryPerColown = ceil(count($countrys) / 4);

        return $this->render('anonymous/home/index.html.twig', [
            "countrys" => array_chunk($countrys, $nbrCountryPerColown, true),
            "nbrCountryPerColown" => $nbrCountryPerColown
        ]);
    }

    /**
     * @Route("/{country}/articles", name="countryArticle")
     */
    public function country_article($country, Request $request)
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $oneCountry = $this->getDoctrine()->getRepository(Country::class)->getCountryByName($country);

        return $this->render('anonymous/article/living-thing/countryLivingThing.html.twig', [
            "country" => $country,
            "articles" => $oneCountry ? $oneCountry->getArticleLivingThing()->getValues() : [],
            "offset" => $offset,
            "nbrOffset" => $offset
        ]);
    }

    /**
     * @Route("/living-thing/{name}", name="articleLivingThing")
     */
    public function article_living_thing($name, Request $request)
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $kingdom = "";

        if($name == "animals") {
            $kingdom = 'Animalia';
        } elseif($name == "insects") {
            $kingdom = 'Insecta';
        } elseif($name == "plants") {
            $kingdom = 'Plantae';
        } elseif($name == "bacteria") {
            $kingdom = 'Bacteria';
        }

        $livingThing = $this->getDoctrine()->getRepository(ArticleLivingThing::class)->getArticleLivingThingsByLivingThingKingdom($kingdom, $offset, $limit);
        $totalOffset = ceil( $this->getDoctrine()->getRepository(ArticleLivingThing::class)->countArticleLivingThingsOffsetByKingdom($kingdom, $limit)['nbrOffset']);

        return $this->render('anonymous/article/living-thing/listLivingThing.html.twig', [
            "livingThing" => $livingThing,
            "name" => $name,
            "offset" => $offset,
            "nbrOffset" => $totalOffset
        ]);
    }

    /**
     * @Route("/living-thing/{name}/{id}", name="articleLivingThingById")
     */
    public function article_living_thing_by_id($name, $id)
    {
        $livingThing = [];
        $kingdom = "";

        if($name == "animals") {
            $kingdom = 'Animalia';
        } elseif($name == "insects") {
            $kingdom = 'Insecta';
        } elseif($name == "plants") {
            $kingdom = 'Plantae';
        } elseif($name == "bacteria") {
            $kingdom = 'Bacteria';
        }

        $livingThing = $this->getDoctrine()->getRepository(ArticleLivingThing::class)->getArticleLivingThingsByLivingThingKingdomByID($kingdom, $id);

        if(empty($livingThing)) {
            return $this->redirectToRoute("404Error");
        }

        return $this->render('anonymous/article/living-thing/singleLivingThing.html.twig', [
            "livingThing" => $livingThing,
            "name" => $name
        ]);
    }

    /**
     * @Route("/about", name="about")
     */
    public function about()
    {
        return $this->render('anonymous/about/index.html.twig');
    }

    /**
     * @Route("/search", name="search")
     */
    public function search(Request $request)
    {
        $search = $request->get('searchInput');
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $livingThing = $this->getDoctrine()->getRepository(ArticleLivingThing::class)->getSearchArticleLivingThings($search);
        
        return $this->render('anonymous/article/living-thing/searchLivingThing.html.twig', [
            "livingThing" => $livingThing,
            "offset" => $offset
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request)
    {
        $formUserLogin = $this->createForm(UserLoginType::class, new User());
        $formUserLogin->handleRequest($request);

        return $this->render('anonymous/user/login.html.twig', [
            "userLoginForm" => $formUserLogin->createView()
        ]);
    }

    /**
     * @Route("/login/check", name="checkUser")
     */
    public function checkUser(TokenStorageInterface $tokenStorage)
    {
        if($tokenStorage->getToken()->getUser()->getRoles()[0] == "ROLE_ADMIN") {
            return $this->redirectToRoute("adminHome");
        } elseif($tokenStorage->getToken()->getUser()->getRoles()[0] == "ROLE_USER") {
            return $this->redirectToRoute("userHome");
        }

        return $this->redirectToRoute("home");
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
            $userRegister->setLogin($userRegister->getEmail());
            $userRegister->setPassword($encoder->encodePassword($userRegister, $userRegister->getPassword()));
            $userRegister->setRoles(['ROLE_USER']);
            $userRegister->setCreatedAt(new \DateTime());
            $manager->persist($userRegister);
            $manager->flush();

            $this->redirectToRoute('login');
        }

        return $this->render('anonymous/user/register.html.twig', [
            "userRegisterForm" => $formUserRegister->createView()
        ]);
    }

    // /**
    //  * @Route("/registerAdmin", name="adminRegister")
    //  */
    // public function admin_register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    // {
    //     $userRegister = new User();
    //     $formUserRegister = $this->createForm(UserRegisterType::class, $userRegister);
    //     $formUserRegister->handleRequest($request);

    //     if($formUserRegister->isSubmitted() && $formUserRegister->isValid()) {
    //         $userRegister->setLogin($userRegister->getEmail());
    //         $userRegister->setPassword($encoder->encodePassword($userRegister, $userRegister->getPassword()));
    //         $userRegister->setRoles(['ROLE_ADMIN']);
    //         $userRegister->setCreatedAt(new \DateTime());
    //         $manager->persist($userRegister);
    //         $manager->flush();

    //         $this->redirectToRoute('login');
    //     }

    //     return $this->render('anonymous/user/register.html.twig', [
    //         "userRegisterForm" => $formUserRegister->createView()
    //     ]);
    // }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        $this->redirectToRoute("home");
    }
}
