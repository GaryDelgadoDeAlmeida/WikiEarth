<?php

namespace App\Controller;

use App\Manager\{PdfGeneratorManager, ContactManager, StatisticsManager};
use App\Form\{UserLoginType, UserRegisterType, ContactType};
use App\Entity\{User, Element, Country, Mineral, LivingThing, Contact, Statistics, Article, ArticleLivingThing, ArticleElement, ArticleMineral};
use Dompdf\{Dompdf, Options};
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
// use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AnonymousController extends AbstractController
{
    private $em;
    private $pdfGeneratorManager;
    private $statisticsManager;
    private $contactManager;
    private $articleRepository;
    
    public function __construct(ContainerInterface $container, EntityManagerInterface $manager)
    {
        $this->em = $manager;
        $this->articleRepository = $manager->getRepository(Article::class);
        $this->statisticsManager = new StatisticsManager($manager);
        // $this->pdfGeneratorManager = new PdfGeneratorManager($container);
        $this->contactManager = new ContactManager();
    }
    
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        // $countrys = $this->em->getRepository(Country::class)->findAll();
        // $nbrCountryPerColown = ceil(count($countrys) / 4);
        $nbrArticles = $this->articleRepository->countArticlesApproved();

        return $this->render('anonymous/home/index.html.twig', [
            // "countrys" => count($countrys) > 0 ? array_chunk($countrys, $nbrCountryPerColown, true) : [],
            // "nbrCountryPerColown" => $nbrCountryPerColown,
            "nbrArticles" => $nbrArticles,
            "nbrLivingThings" => $this->em->getRepository(LivingThing::class)->countLivingThings(),
            "nbrElements" => $this->em->getRepository(Element::class)->countElements(),
            "nbrMinerals" => $this->em->getRepository(Mineral::class)->countMinerals(),
        ]);
    }

    /**
     * @Route("/news", name="news")
     */
    public function news_page(Request $request)
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? intval($request->get('offset')) : 1;

        return $this->render("anonymous/article/news.html.twig", [
            "articles" => $this->articleRepository->getArticlesApproved($offset, $limit),
            "limit" => $limit,
            "offset" => $offset,
            "nbrOffset" => ceil($this->articleRepository->countArticlesApproved() / $limit)
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

        return $this->render('anonymous/article/country.html.twig', [
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
        $kingdom = ucfirst($name);

        $livingThing = $this->articleRepository->getArticleLivingThingsByLivingThingKingdom($kingdom, $offset, $limit);
        $totalOffset = ceil($this->articleRepository->countArticleLivingThingsByKingdom($kingdom, $limit));

        return $this->render('anonymous/article/living-thing/list.html.twig', [
            "livingThing" => $livingThing,
            "name" => $name,
            "offset" => $offset,
            "nbrOffset" => $totalOffset
        ]);
    }

    /**
     * @Route("/living-thing/{name}/{id}", name="articleLivingThingByID")
     */
    public function article_living_thing_by_id(string $name, int $id)
    {
        $articleLivingThing = [];
        $kingdom = ucfirst($name);

        $articleLivingThing = $this->articleRepository->getArticleLivingThingsByLivingThingKingdomByID($kingdom, $id);

        // S'il est vide (soit il n'existe pas, soit l'article n'est pas encore approuver) alors ...
        if(empty($articleLivingThing)) {
            return $this->redirectToRoute("articleLivingThing", [
                "name" => $name,
                "class" => "danger",
                "message" => "This living thing does not exist."
            ], 307);
        }

        // Article consulatation statistics
        $this->statisticsManager->updateArticlePageConsultationsStatistics();

        return $this->render('anonymous/article/living-thing/single.html.twig', [
            "article" => $articleLivingThing,
            "mediaGallery" => $articleLivingThing->getMediaGallery(),
            "references" => [],
            "countries" => $articleLivingThing->getLivingThing()->getCountries(),
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
        $nbrOffset = ceil($this->em->getRepository(Article::class)->countArticleElementsApproved() / $limit);

        return $this->render('anonymous/article/natural-elements/list.html.twig', [
            "elements" => $this->em->getRepository(Article::class)->getArticleElementsApproved($offset, $limit),
            "offset" => $offset,
            "nbrOffset" => $nbrOffset,
        ]);
    }

    /**
     * @Route("/element/{id}", name="articleElementByID")
     */
    public function article_element_by_id(int $id)
    {
        $element = $this->em->getRepository(Article::class)->getArticleElement($id);

        if(empty($element)) {
            return $this->redirectToRoute('articleElement');
        }

        // Article consulatation statistics
        $this->statisticsManager->updateArticlePageConsultationsStatistics();

        return $this->render('anonymous/article/natural-elements/single.html.twig', [
            "article" => $element,
            "mediaGallery" => [],
            "references" => [],
        ]);
    }

    /**
     * @Route("/mineral", name="articleMineral")
     */
    public function article_mineral(Request $request)
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $nbrOffset = ceil($this->em->getRepository(Article::class)->countArticleMineralsApproved() / $limit);

        return $this->render('anonymous/article/minerals/list.html.twig', [
            "minerals" => $this->em->getRepository(Article::class)->getArticleMineralsApproved($offset, $limit),
            "offset" => $offset,
            "nbrOffset" => $nbrOffset,
        ]);
    }

    /**
     * @Route("/mineral/{id}", name="articleMineralByID")
     */
    public function article_mineral_by_id(int $id)
    {
        $article = $this->em->getRepository(Article::class)->getArticleMineral($id);

        if(empty($article)) {
            return $this->redirectToRoute('404Error');
        }

        // Article consulatation statistics
        $this->statisticsManager->updateArticlePageConsultationsStatistics();
        
        return $this->render('anonymous/article/minerals/single.html.twig', [
            "article" => $article,
            "mediaGallery" => [],
            "references" => [],
            "countries" => $article->getArticleMineral()->getMineral()->getCountry()
        ]);
    }

    /**
     * @Route("/chimical-reaction", name="articleChimicalReaction")
     */
    public function article_chimical_reactions(Request $request)
    {
        return $this->render("anonymous/article/chimical-reactions/list.html.twig", []);
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
        $articles = $this->em->getRepository(Article::class)->searchArticles($search, $offset, $limit);
        $nbrArticles = $this->em->getRepository(Article::class)->countSearchedArticles($search);
        $nbrOffset = ceil($nbrArticles / $limit);
        
        return $this->render('anonymous/article/search.html.twig', [
            "articles" => $articles,
            "offset" => $offset,
            "nbrOffset" => $nbrOffset,
            "search" => $search,
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
    public function checkUser(TokenStorageInterface $tokenStorage)
    {
        // Authentified user connection statistics
        $this->statisticsManager->updateUserConnectionStatistics();

        if($tokenStorage->getToken()->getUser() instanceof User) {
            if($tokenStorage->getToken()->getUser()->hasRole("ROLE_ADMIN")) {
                return $this->redirectToRoute("adminHome");
            } elseif($tokenStorage->getToken()->getUser()->hasRole("ROLE_USER")) {
                return $this->redirectToRoute("userHome");
            }
        }

        return $this->redirectToRoute("login");
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

                $this->contactManager->sendEmailToAdmin($userRegister->getEmail(), "A new WikiEarth user", "The user {$userRegister->getFirstname()} {$userRegister->getLastname()} ({$userRegister->getEmail()}) created an account on WikiEarth.");
                $this->contactManager->sendEmailToUser($userRegister->getEmail(), "Welcome to WikiEarth", "You account {$userRegister->getLogin()} has been created.\n\n");

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
    public function admin_register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $userRegister = new User();
        $formUserRegister = $this->createForm(UserRegisterType::class, $userRegister);
        $formUserRegister->handleRequest($request);
        $response = [];

        if($formUserRegister->isSubmitted() && $formUserRegister->isValid()) {
            $userRegister->setPassword($encoder->encodePassword($userRegister, $userRegister->getPassword()));
            $userRegister->setRoles(['ROLE_ADMIN']);
            $userRegister->setCreatedAt(new \DateTime());
            $manager->persist($userRegister);
            $manager->flush();

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
    public function logout()
    {
        $this->redirectToRoute("home");
    }
}
