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
        $articles = $this->articleRepository->searchArticles($search, $offset, $limit);
        $nbrArticles = $this->articleRepository->countSearchedArticles($search);
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
    public function contact(Request $request)
    {
        $formContact = $this->createForm(ContactType::class, $contact = new Contact());
        $formContact->handleRequest($request);
        $response = [];

        if($formContact->isSubmitted() && $formContact->isValid()) {
            $contact->setCreatedAt(new \DateTime());
            $this->em->persist($contact);
            $this->em->commit();
            $this->em->flush();

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
}
