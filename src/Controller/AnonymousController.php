<?php

namespace App\Controller;

use Dompdf\{Dompdf, Options};
use App\Manager\PdfGeneratorManager;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\{UserLoginType, UserRegisterType};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Entity\{User, Element, Country, Mineral, LivingThing, ArticleLivingThing, ArticleElement, ArticleMineral};

class AnonymousController extends AbstractController
{
    private $pdfGeneratorManager;
    
    public function __construct(ContainerInterface $container)
    {
        $this->pdfGeneratorManager = new PdfGeneratorManager($container);
    }
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        $countrys = $this->getDoctrine()->getRepository(Country::class)->findAll();
        $nbrCountryPerColown = ceil(count($countrys) / 4);

        return $this->render('anonymous/home/index.html.twig', [
            "countrys" => array_chunk($countrys, $nbrCountryPerColown, true),
            "nbrCountryPerColown" => $nbrCountryPerColown,
            "nbrArticles" => $this->getDoctrine()->getRepository(ArticleLivingThing::class)->countArticleLivingThingsApproved(),
            "nbrLivingThings" => $this->getDoctrine()->getRepository(LivingThing::class)->countLivingThings(),
            "nbrElements" => $this->getDoctrine()->getRepository(Element::class)->countElements(),
            "nbrMinerals" => $this->getDoctrine()->getRepository(Mineral::class)->countMinerals(),
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

        $livingThing = $this->getDoctrine()->getRepository(ArticleLivingThing::class)->getArticleLivingThingsByLivingThingKingdom($kingdom, $offset, $limit);
        $totalOffset = ceil( $this->getDoctrine()->getRepository(ArticleLivingThing::class)->countArticleLivingThingsByKingdom($kingdom, $limit)['nbrOffset']);

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

        $livingThing = $this->getDoctrine()->getRepository(ArticleLivingThing::class)->getArticleLivingThingsByLivingThingKingdomByID($kingdom, $id);

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
     * @Route("/living-thing/{name}/{id}/pdf", name="exportArticleLivingThingById")
     */
    public function export_to_pdf_article_living_thing_by_id($name, $id)
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

        // S'il est vide (soit il n'existe pas, soit l'article n'est pas encore approuver) alors ...
        if(empty($livingThing)) {
            return $this->redirectToRoute("404Error");
        }

        return new Response($this->pdfGeneratorManager->generatePdf($livingThing, "living-thing"), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * @Route("/element", name="articleElement")
     */
    public function article_element(Request $request)
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $nbrOffset = ceil($this->getDoctrine()->getRepository(ArticleElement::class)->countArticleElements() / $limit);

        return $this->render('anonymous/article/natural-elements/list.html.twig', [
            "elements" => $this->getDoctrine()->getRepository(ArticleElement::class)->getArticleElements($offset, $limit),
            "offset" => $offset,
            "nbrOffset" => $nbrOffset,
        ]);
    }

    /**
     * @Route("/element/{id}", name="articleElementByID")
     */
    public function article_element_by_id($id)
    {
        $element = $this->getDoctrine()->getRepository(ArticleElement::class)->find($id);

        if(!empty($element)) {
            return $this->redirectToRoute('404Error');
        }

        return $this->render('anonymous/article/natural-elements/single.html.twig', [
            "element" => $element
        ]);
    }

    /**
     * @Route("/element/{id}/pdf", name="exportArticleElementByID")
     */
    public function export_to_pdf_article_element_by_id($id)
    {
        $element = $this->getDoctrine()->getRepository(ArticleElement::class)->find($id);

        if(!empty($element)) {
            return $this->redirectToRoute('404Error');
        }

        return new Response($this->pdfGeneratorManager->generatePdf($element, "natural-element"), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * @Route("/mineral", name="articleMineral")
     */
    public function article_mineral(Request $request)
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $nbrOffset = ceil($this->getDoctrine()->getRepository(ArticleMineral::class)->countArticleMinerals() / $limit);

        return $this->render('anonymous/article/minerals/list.html.twig', [
            "minerals" => $this->getDoctrine()->getRepository(ArticleMineral::class)->getArticleMinerals($offset, $limit),
            "offset" => $offset,
            "nbrOffset" => $nbrOffset,
        ]);
    }

    /**
     * @Route("/mineral/{id}", name="articleMineralByID")
     */
    public function article_mineral_by_id($id)
    {
        $mineral = $this->getDoctrine()->getRepository(ArticleMineral::class)->find($id);

        if(!empty($mineral)) {
            return $this->redirectToRoute('404Error');
        }

        return $this->render('anonymous/article/minerals/single.html.twig', [
            "mineral" => $mineral
        ]);
    }

    /**
     * @Route("/mineral/{id}/pdf", name="exportArticleMineralByID")
     */
    public function export_to_pdf_article_mineral_by_id($id)
    {
        $mineral = $this->getDoctrine()->getRepository(ArticleMineral::class)->find($id);

        if(!empty($mineral)) {
            return $this->redirectToRoute('404Error');
        }

        return new Response($this->pdfGeneratorManager->generatePdf($mineral, "mineral"), 200, [
            'Content-Type' => 'application/pdf',
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
        $livingThing = $this->getDoctrine()->getRepository(ArticleLivingThing::class)->searchArticleLivingThings($search);
        
        return $this->render('anonymous/article/living-thing/search.html.twig', [
            "livingThing" => $livingThing,
            "offset" => $offset
        ]);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request)
    {
        $formContact = $this->createForm(ArticleLivingThingType::class, null);
        $formContact->handleRequest($request);

        if($formContact->isSubmitted() && $formContact->isValid()) {
            // 
        }
        
        return $this->render('anonymous/contact/index.html.twig', [
            "formContact" => $formContact->createView()
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
