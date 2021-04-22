<?php

namespace App\Controller;

use App\Manager\{PdfGeneratorManager, ContactManager};
use App\Form\{UserLoginType, UserRegisterType, ContactType};
use App\Entity\{User, Element, Country, Mineral, LivingThing, Contact, ArticleLivingThing, ArticleElement, ArticleMineral};
use Dompdf\{Dompdf, Options};
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AnonymousController extends AbstractController
{
    private $pdfGeneratorManager;
    private $contactManager;
    private $em;
    
    public function __construct(ContainerInterface $container, EntityManagerInterface $manager)
    {
        // $this->pdfGeneratorManager = new PdfGeneratorManager($container);
        $this->contactManager = new ContactManager();
        $this->em = $manager;
    }
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        $countrys = $this->em->getRepository(Country::class)->findAll();
        $nbrCountryPerColown = ceil(count($countrys) / 4);
        $nbrArticles = 
            $this->em->getRepository(ArticleLivingThing::class)->countArticleLivingThingsApproved() + 
            $this->em->getRepository(ArticleElement::class)->countArticleElementsApprouved() +
            $this->em->getRepository(ArticleMineral::class)->countArticleMineralsApprouved()
        ;

        return $this->render('anonymous/home/index.html.twig', [
            // "countrys" => array_chunk($countrys, $nbrCountryPerColown, true),
            // "nbrCountryPerColown" => $nbrCountryPerColown,
            "nbrArticles" => $nbrArticles,
            // "nbrLivingThings" => $this->em->getRepository(LivingThing::class)->countLivingThings(),
            "nbrElements" => $this->em->getRepository(Element::class)->countElements(),
            "nbrMinerals" => $this->em->getRepository(Mineral::class)->countMinerals(),
        ]);
    }

    /**
     * @Route("/{country}/articles", name="countryArticle")
     */
    public function country_article($country, Request $request)
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $oneCountry = $this->em->getRepository(Country::class)->getCountryByName($country);
        $articles = [];

        foreach($oneCountry->getLivingThing()->getValues() as $oneLivingThing) {
            if(!empty($oneLivingThing->getArticleLivingThing())) {
                $articles[] = $oneLivingThing->getArticleLivingThing();
            }
        }

        return $this->render('anonymous/article/living-thing/country.html.twig', [
            "country" => $country,
            "articles" => $articles,
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

        $livingThing = $this->em->getRepository(ArticleLivingThing::class)->getArticleLivingThingsByLivingThingKingdom($kingdom, $offset, $limit);
        $totalOffset = ceil( $this->em->getRepository(ArticleLivingThing::class)->countArticleLivingThingsByKingdom($kingdom, $limit)['nbrOffset']);

        return $this->render('anonymous/article/living-thing/list.html.twig', [
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

        $livingThing = $this->em->getRepository(ArticleLivingThing::class)->getArticleLivingThingsByLivingThingKingdomByID($kingdom, $id);

        // S'il est vide (soit il n'existe pas, soit l'article n'est pas encore approuver) alors ...
        if(empty($livingThing)) {
            return $this->redirectToRoute("404Error");
        }

        // dd($livingThing->getMediaGallery());

        return $this->render('anonymous/article/living-thing/single.html.twig', [
            "livingThing" => $livingThing,
            "mediaGallery" => $livingThing->getMediaGallery(),
            "references" => [],
            "countries" => $livingThing->getIdLivingThing()->getCountries(),
            "name" => $name
        ]);
    }

    /**
     * @Route("/element", name="articleElement")
     */
    public function article_element(Request $request)
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $nbrOffset = ceil($this->em->getRepository(ArticleElement::class)->countArticleElements() / $limit);

        return $this->render('anonymous/article/natural-elements/list.html.twig', [
            "elements" => $this->em->getRepository(ArticleElement::class)->getArticleElementsApprouved($offset, $limit),
            "offset" => $offset,
            "nbrOffset" => $nbrOffset,
        ]);
    }

    /**
     * @Route("/element/{id}", name="articleElementByID")
     */
    public function article_element_by_id($id)
    {
        $element = $this->em->getRepository(ArticleElement::class)->find($id);

        if(!empty($element)) {
            return $this->redirectToRoute('404Error');
        }

        return $this->render('anonymous/article/natural-elements/single.html.twig', [
            "element" => $element
        ]);
    }

    /**
     * @Route("/mineral", name="articleMineral")
     */
    public function article_mineral(Request $request)
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $nbrOffset = ceil($this->em->getRepository(ArticleMineral::class)->countArticleMinerals() / $limit);

        return $this->render('anonymous/article/minerals/list.html.twig', [
            "minerals" => $this->em->getRepository(ArticleMineral::class)->getArticleMineralsApprouved($offset, $limit),
            "offset" => $offset,
            "nbrOffset" => $nbrOffset,
        ]);
    }

    /**
     * @Route("/mineral/{id}", name="articleMineralByID")
     */
    public function article_mineral_by_id($id)
    {
        $articleMineral = $this->em->getRepository(ArticleMineral::class)->find($id);

        if(empty($articleMineral)) {
            return $this->redirectToRoute('404Error');
        }

        return $this->render('anonymous/article/minerals/single.html.twig', [
            "articleMineral" => $articleMineral,
            "mediaGallery" => [],
            "references" => [],
            "countries" => $articleMineral->getMineral()->getCountry()
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
     * @Route("/policy", name="policy")
     */
    public function policy()
    {
        return $this->render('anonymous/policy/index.html.twig');
    }

    /**
     * @Route("/search", name="search")
     */
    public function search(Request $request)
    {
        $search = $request->get('searchInput');
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $livingThing = $this->em->getRepository(ArticleLivingThing::class)->searchArticleLivingThings($search);
        
        return $this->render('anonymous/article/living-thing/search.html.twig', [
            "livingThing" => $livingThing,
            "offset" => $offset
        ]);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request, EntityManagerInterface $manager)
    {
        $contact = new Contact();
        $formContact = $this->createForm(ContactType::class, $contact);
        $formContact->handleRequest($request);
        $response = [];

        if($formContact->isSubmitted() && $formContact->isValid()) {
            $contact->setCreatedAt(new \DateTime());
            $manager->persist($contact);
            $manager->commit();
            $manager->flush();

            if($this->contactManager->sendEmailToAdmin($contact->getEmail(), $contact->getSubject(), $contact->getContent())) {
                $response = [
                    "error" => false,
                    "class" => "success",
                    "message" => "Votre message a été envoyé avec succès"
                ];
            } else {
                $response = [
                    "error" => true,
                    "class" => "danger",
                    "message" => "Votre message n'a pu être envoyer. Veuillez réessayer plus tard."
                ];
            }
        }
        
        return $this->render('anonymous/contact/index.html.twig', [
            "formContact" => $formContact->createView(),
            "response" => $response
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
        // Retourne l'erreur d'authentification rencontrée
        $error = $authenticationUtils->getLastAuthenticationError();
        
        $formUserLogin = $this->createForm(UserLoginType::class, new User());
        $formUserLogin->handleRequest($request);

        return $this->render('anonymous/user/login.html.twig', [
            "userLoginForm" => $formUserLogin->createView(),
            "error" => $error,
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
        // throw $this->createNotFoundException("This role don't exist");
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $userRegister = new User();
        $formUserRegister = $this->createForm(UserRegisterType::class, $userRegister);
        $formUserRegister->handleRequest($request);
        $response = [];

        if($formUserRegister->isSubmitted() && $formUserRegister->isValid()) {
            // If not occurence then there is no user with the chosen login
            if(empty($manager->getRepository(User::class)->getUserByLogin($userRegister->getLogin()))) {
                $userRegister->setPassword($encoder->encodePassword($userRegister, $userRegister->getPassword()));
                $userRegister->setRoles(['ROLE_USER']);
                $userRegister->setCreatedAt(new \DateTime());
                $manager->persist($userRegister);
                $manager->flush();

                $this->contactManager->sendEmailToAdmin($userRegister->getEmail(), "A new GemEarth user", "The user {$userRegister->getFirstname()} {$userRegister->getLastname()} ({$userRegister->getEmail()}) created an account on GemEarth.");
                $this->contactManager->sendEmailToUser($userRegister->getEmail(), "Welcome to GemEarth", "You account {$userRegister->getLogin()} has been created.\n\n");

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

    // /**
    //  * @Route("/registerAdmin", name="adminRegister")
    //  */
    // public function admin_register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    // {
    //     $userRegister = new User();
    //     $formUserRegister = $this->createForm(UserRegisterType::class, $userRegister);
    //     $formUserRegister->handleRequest($request);

    //     if($formUserRegister->isSubmitted() && $formUserRegister->isValid()) {
    //         $userRegister->setPassword($encoder->encodePassword($userRegister, $userRegister->getPassword()));
    //         $userRegister->setRoles(['ROLE_ADMIN']);
    //         $userRegister->setCreatedAt(new \DateTime());
    //         $manager->persist($userRegister);
    //         $manager->flush();

    //         return $this->redirectToRoute('login');
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
