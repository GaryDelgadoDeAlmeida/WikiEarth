<?php

namespace App\Controller;

use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Manager\{
    UserManager, 
    ContactManager, 
    LivingThingManager, 
    ElementManager, 
    MineralManager, 
    ArticleManager, 
    ArticleLivingThingManager, 
    ArticleElementManager, 
    ArticleMineralManager, 
    MediaGalleryManager
};
use App\Form\{
    UserType, 
    LivingThingType, 
    MineralType, 
    ElementType, 
    UserRegisterType, 
    ArticleLivingThingType, 
    ArticleElementType, 
    ArticleMineralType
};
use App\Entity\{
    User, 
    Element, 
    Mineral, 
    Statistics, 
    SourceLink, 
    LivingThing, 
    MediaGallery, 
    Notification, 
    Article, 
    ArticleLivingThing, 
    ArticleElement, 
    ArticleMineral
};

class AdminController extends AbstractController
{
    private $current_logged_user;
    private $livingThingManager;
    private $elementManager;
    private $mineralManager;
    private $articleManager;
    private $articleLivingThingManager;
    private $articleElementManager;
    private $articleMineralManager;
    private $mediaGalleryManager;
    private $userManager;
    private $contactManager;
    private $em;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $em, ContainerInterface $container)
    {
        $this->current_logged_user = $tokenStorage->getToken()->getUser();
        $this->livingThingManager = new LivingThingManager($container);
        $this->elementManager = new ElementManager($container);
        $this->mineralManager = new MineralManager($container);
        $this->articleManager = new ArticleManager();
        $this->articleLivingThingManager = new ArticleLivingThingManager();
        $this->articleElementManager = new ArticleElementManager();
        $this->articleMineralManager = new ArticleMineralManager();
        $this->mediaGalleryManager = new MediaGalleryManager($container);
        $this->userManager = new UserManager();
        $this->contactManager = new ContactManager();
        $this->em = $em;
    }
    
    /**
     * @Route("/admin", name="adminHome")
     */
    public function admin_home()
    {
        $articleRepo = $this->em->getRepository(Article::class);
        $nbrArticles = $articleRepo->countArticlesApproved();
        $articles = $articleRepo->getArticles(1, 5);
        $past_month_date = $current_date = new \DateTimeImmutable();
        $past_month_date = $past_month_date->modify("-6 month");
        $latestStatistics = $this->em->getRepository(Statistics::class)->getStatisticsByDateInterval($current_date, $past_month_date);
        $latestUsersActivities = [];

        // We only get the user connection part
        foreach($latestStatistics as $oneLatestStatistic) {
            $latestUsersActivities[] = [
                "label" => $oneLatestStatistic->getCreatedAt()->format("M"),
                "y" => $oneLatestStatistic->getNbrUsersConnection()
            ];
        }

        return $this->render('admin/home/index.html.twig', [
            "nbrUsers" => $this->em->getRepository(User::class)->countUsers($this->current_logged_user->getId()),
            "nbrArticles" => $nbrArticles,
            "nbrLivingThings" => $this->em->getRepository(LivingThing::class)->countLivingThings(),
            "nbrElements" => $this->em->getRepository(Element::class)->countElements(),
            "nbrMinerals" => $this->em->getRepository(Mineral::class)->countMinerals(),
            "nbrChimicalReaction" => 0,
            "articles" => $articles,
            "dataPoints" => $latestUsersActivities,
        ]);
    }

    /**
     * @Route("/admin/profile", name="adminProfile")
     */
    public function admin_profile(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $formUser = $this->createForm(UserType::class, $this->current_logged_user);
        $formUser->handleRequest($request);
        $response = [];

        if($formUser->isSubmitted() && $formUser->isValid()) {
            $response = $this->userManager->updateUser(
                $formUser, 
                $this->current_logged_user, 
                $this->em, 
                $encoder, 
                $this->getParameter('project_users_dir')
            );
        }

        return $this->render('admin/user/profile.html.twig', [
            "userForm" => $formUser->createView(),
            "userImg" => $this->current_logged_user->getImgPath() ? $this->current_logged_user->getImgPath() : "https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1024px-User_icon_2.svg.png",
            "response" => $response
        ]);
    }

    /**
     * @Route("/admin/users", name="adminUsersListing")
     */
    public function admin_users_listing(Request $request)
    {
        $limit = 15;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $nbrUsers = $this->em->getRepository(User::class)->countUsers($this->current_logged_user->getId());
        $nbrOffset = $nbrUsers > $limit ? ceil($nbrUsers / $limit) : 1;

        return $this->render('admin/user/listUsers.html.twig', [
            "users" => $this->em->getRepository(User::class)->getUsers($offset - 1, $limit, $this->current_logged_user->getId()),
            "offset" => $offset,
            "total_page" => $nbrOffset
        ]);
    }

    /**
     * @Route("/admin/users/add", name="adminUserAdd")
     */
    public function admin_user_add(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $response = [];
        $user = new User();
        $formAddUser = $this->createForm(UserRegisterType::class, $user);
        $formAddUser->handleRequest($request);

        if($formAddUser->isSubmitted() && $formAddUser->isValid()) {
            try {
                if(empty($this->em->getRepository(User::class)->getUserByLogin(trim($user->getLogin())))) {
                    if(trim($user->getPassword()) == trim($formAddUser["confirmPassword"]->getData())) {
                        $user->setLogin(trim($user->getLogin()));
                        $user->setPassword($encoder->encodePassword($user, trim($user->getPassword())));
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
            "formUser" => $formAddUser->createView(),
            "response" => $response
        ]);
    }

    /**
     * @Route("/admin/users/{id}", name="adminUserEdit")
     */
    public function admin_user_edit(User $user, Request $request, UserPasswordEncoderInterface $encoder)
    {
        $formUser = $this->createForm(UserType::class, $user);
        $formUser->handleRequest($request);
        $response = [];

        if($formUser->isSubmitted() && $formUser->isValid()) {
            $response = $this->userManager->updateUser(
                $formUser, 
                $user, 
                $this->em, 
                $encoder, 
                $this->getParameter('project_users_dir')
            );

            $this->redirectToRoute('adminUsersListing');
        }

        return $this->render('admin/user/profile.html.twig', [
            "userForm" => $formUser->createView(),
            "userImg" => $user->getImgPath() ? $user->getImgPath() : "https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1024px-User_icon_2.svg.png",
            "response" => $response
        ]);
    }
    
    /**
     * @Route("/admin/users/{id}/delete", name="adminUserDelete")
     */
    public function admin_user_delete(User $user)
    {
        foreach($user->getNotifications() as $oneNotification) {
            $this->em->remove($oneNotification);
        }
        $this->em->remove($user);
        $this->em->flush();

        return $this->redirectToRoute('adminUsersListing', [
            "response" => [
                "class" => "success",
                "content" => "L'utilisateur {$user->getFirstname()} {$user->getLastname()} a bien été supprimé."
            ]
        ], 302);
    }

    /**
     * @Route("/admin/living-thing", name="adminLivingThing")
     */
    public function admin_living_thing(Request $request)
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $search = !empty($request->get("search")) ? $request->get("search") : null;
        $nbrLivingThing = $livingThings = [];

        if(!empty($search)) {
            $livingThings = $this->em->getRepository(LivingThing::class)->searchLivingThing($search, $offset, $limit);
            $nbrLivingThing = $this->em->getRepository(LivingThing::class)->countSearchLivingThing($search);
        } else {
            $livingThings = $this->em->getRepository(LivingThing::class)->getLivingThings($offset, $limit);
            $nbrLivingThing = $this->em->getRepository(LivingThing::class)->countLivingThings();
        }

        $nbrOffset = $nbrLivingThing > $limit ? ceil($nbrLivingThing / $limit) : 1;

        return $this->render('admin/article/living-thing/listLivingThing.html.twig', [
            "livingThings" => $livingThings,
            "offset" => $offset,
            "nbrOffset" => $nbrOffset,
            "search" => $search,
        ]);
    }

    /**
     * @Route("/admin/living-thing/add", name="adminAddLivingThing")
     */
    public function admin_add_living_thing(Request $request)
    {
        $livingThing = new LivingThing();
        $formLivingThing = $this->createForm(LivingThingType::class, $livingThing);
        $formLivingThing->handleRequest($request);
        $message = [];

        if($formLivingThing->isSubmitted() && $formLivingThing->isValid()) {
            $message = $this->livingThingManager->setLivingThing(
                $formLivingThing["imgPath"]->getData(), 
                $livingThing, 
                $this->em
            );
        }

        return $this->render('admin/article/living-thing/formLivingThing.html.twig', [
            "formLivingThing" => $formLivingThing->createView(),
            "response" => $message
        ]);
    }

    /**
     * @Route("/admin/living-thing/{id}/article", name="adminLivingThingCreateArticle")
     */
    public function admin_living_thing_create_article($id, Request $request)
    {
        $articleLivingThing = $this->em->getRepository(Article::class)->getArticleByLivingThing($id);
        $message = [];

        // If empty then there is no article so the user can create the article
        if(empty($articleLivingThing)) {
            $articleLivingThing = new ArticleLivingThing();
            $livingThing = $this->em->getRepository(LivingThing::class)->getLivingThing($id);

            if(!empty($livingThing)) {
                $formArticle = $this->createForm(ArticleLivingThingType::class, $articleLivingThing);
                $formArticle->get('livingThing')->setData($livingThing);
                $formArticle->handleRequest($request);

                if($formArticle->isSubmitted() && $formArticle->isValid()) {
                    $message = $this->articleLivingThingManager->setArticleLivingThing(
                        $articleLivingThing,
                        $livingThing,
                        $this->em,
                        $this->current_logged_user
                    );
                }
            } else {
                return $this->redirectToRoute("adminLivingThing", [
                    "class" => "danger",
                    "message" => "This living thing does not exist"
                ], 307);
            }
        } else {
            return $this->redirectToRoute("adminLivingThing", [
                "class" => "danger",
                "message" => "This living thing already have an article"
            ], 307);
        }

        return $this->render('admin/article/living-thing/formArticle.html.twig', [
            "formArticle" => $formArticle->createView(),
            "response" => $message
        ]);
    }

    /**
     * @Route("/admin/living-thing/{id}/edit", name="adminEditLivingThing")
     */
    public function admin_edit_living_thing(LivingThing $livingThing, Request $request)
    {
        $formLivingThing = $this->createForm(LivingThingType::class, $livingThing);
        $formLivingThing->handleRequest($request);
        $message = [];

        if($formLivingThing->isSubmitted() && $formLivingThing->isValid()) {
            $message = $this->livingThingManager->setLivingThing(
                $formLivingThing["imgPath"]->getData(),
                $livingThing, 
                $this->em
            );
        }

        return $this->render('admin/article/living-thing/formLivingThing.html.twig', [
            "formLivingThing" => $formLivingThing->createView(),
            "response" => $message
        ]);
    }

    /**
     * Possibilité d'en faire une retour API
     * 
     * Attention : supprimer un living thing possèdant une liaison avec une autre table,
     * la donnée dans l'autre table et le living thing seront supprimés de la base de données.
     * 
     * @Route("/admin/living-thing/{id}/delete", name="adminDeleteLivingThing")
     */
    public function admin_delete_living_thing(LivingThing $livingThing)
    {
        if(!empty($livingThing->getImgPath())) {
            unlink($this->getParameter('project_public_dir') . $livingThing->getImgPath());
        }

        foreach($livingThing->getCountries() as $oneCountry) {
            $livingThing->removeCountry($oneCountry);
        }

        $this->em->remove($livingThing);
        $this->em->flush();

        return $this->redirectToRoute('adminLivingThing');
    }

    /**
     * @Route("/admin/element", name="adminElement")
     */
    public function admin_element(Request $request)
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? \intval($request->get('offset')) : 1;
        $search = !empty($request->get('search')) ? $request->get('search') : null;
        $elements = [];
        $nbrElements = 0;

        if(!empty($search)) {
            $elements = $this->em->getRepository(Element::class)->searchElements($search, $offset, $limit);
            $nbrElements = $this->em->getRepository(Element::class)->countSearchElements($search);
        } else {
            $elements = $this->em->getRepository(Element::class)->getElements($offset, $limit);
            $nbrElements = $this->em->getRepository(Element::class)->countElements();
        }

        $nbrOffset = $nbrElements > $limit ? ceil($nbrElements / $limit) : 0;

        return $this->render('admin/article/natural-elements/listElement.html.twig', [
            "elements" => $elements,
            "offset" => $offset,
            "nbrOffset" => $nbrOffset,
            "search" => $search,
        ]);
    }

    /**
     * @Route("/admin/element/add", name="adminAddElement")
     */
    public function admin_add_element(Request $request)
    {
        $element = new Element();
        $formElement = $this->createForm(ElementType::class, $element);
        $formElement->handleRequest($request);
        $response = [];

        if($formElement->isSubmitted() && $formElement->isValid()) {
            
            // On vérifie qu'il n'existe pas déjà un element du tableau périodique portant le même nom dans la base de données
            if(empty($this->em->getRepository(Element::class)->getElementByScientificName($element->getScientificName()))) {
                $response = $this->elementManager->setElement(
                    $formElement["imgPath"]->getData(), 
                    $element,
                    $formElement,
                    $this->em
                );
            } else {
                $response = [
                    "class" => "danger",
                    "message" => "The element {$element->getScientificName()} already exist in the databse."
                ];
            }
        }
        return $this->render('admin/article/natural-elements/formElement.html.twig', [
            "formElement" => $formElement->createView(),
            "response" => $response
        ]);
    }

    /**
     * @Route("/admin/element/{id}/edit", name="adminEditElement")
     */
    public function admin_edit_element(int $id, Request $request)
    {
        $element = $this->em->getRepository(Element::class)->find($id);
        
        if(empty($element)) {
            throw new \Exception("This element hasn't been found");
        }

        $formElement = $this->createForm(ElementType::class, $element);
        $formElement->get("volumicMass")->setData(implode(" || ", $element->getVolumicMass()));
        $formElement->handleRequest($request);
        $response = [];

        if($formElement->isSubmitted() && $formElement->isValid()) {
            $response = $this->elementManager->setElement(
                $formElement["imgPath"]->getData(), 
                $element,
                $formElement,
                $this->em
            );
        }
        return $this->render('admin/article/natural-elements/formElement.html.twig', [
            "formElement" => $formElement->createView(),
            "response" => $response
        ]);
    }

    /**
     * @Route("/admin/element/{id}/delete", name="adminDeleteElement")
     */
    public function admin_delete_element(int $id, Request $request)
    {
        $element = $this->em->getRepository(Element::class)->find($id);
        
        if(empty($element)) {
            throw new \Exception("This element hasn't been found");
        }

        $this->em->remove($element);
        $this->em->flush();

        return $this->json([
            "error" => false,
            "class" => "success",
            "message" => "The element {$element->getName()} has been deleted"
        ]);
    }

    /**
     * @Route("/admin/mineral", name="adminMineral")
     */
    public function admin_mineral(Request $request)
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $search = !empty($request->get('search')) ? $request->get('search') : null;
        $minerals = [];
        $nbrPages = 1;

        if(!empty($search)) {
            $minerals = $this->em->getRepository(Mineral::class)->searchMineral($search, $offset, $limit);
            $nbrPages = ceil($this->em->getRepository(Mineral::class)->countSearchMineral($search) / $limit);
        } else {
            $minerals = $this->em->getRepository(Mineral::class)->getMinerals($offset, $limit);
            $nbrPages = ceil($this->em->getRepository(Mineral::class)->countMinerals() / $limit);
        }

        return $this->render('admin/article/minerals/listMineral.html.twig', [
            "offset" => $offset,
            "nbrOffset" => $nbrPages,
            "minerals" => $minerals,
            "search" => $search,
        ]);
    }

    /**
     * @Route("/admin/mineral/add", name="adminAddMineral")
     */
    public function admin_add_mineral(Request $request)
    {
        $mineral = new Mineral();
        $formMineral = $this->createForm(MineralType::class, $mineral);
        $formMineral->handleRequest($request);
        $response = [];

        if($formMineral->isSubmitted() && $formMineral->isValid()) {
            // On vérifie qu'il n'existe pas déjà un mineral portant le même nom dans la base de données
            if(empty($this->em->getRepository(Mineral::class)->getMineralByName($mineral->getName()))) {
                $response = $this->mineralManager->setMineral(
                    $formMineral["imgPath"]->getData(), 
                    $mineral,
                    $formMineral,
                    $this->em
                );
            } else {
                $response = [
                    "class" => "danger",
                    "message" => "The mineral {$mineral->getName()} already exist in the databse."
                ];
            }
        }
        return $this->render('admin/article/minerals/formMineral.html.twig', [
            "formMineral" => $formMineral->createView(),
            "response" => $response
        ]);
    }

    /**
     * @Route("/admin/mineral/{id}/edit", name="adminEditMineral")
     */
    public function admin_mineral_edit_by_id(int $id, Request $request)
    {
        $mineral = $this->em->getRepository(Mineral::class)->findOneBy(["id" => $id]);

        if(empty($mineral)) {
            throw new \Exception("The mineral with the id {$id} wasn't found.");
        }

        $formMineral = $this->createForm(MineralType::class, $mineral);
        $formMineral->get('imaStatus')->setData(implode(", ", $mineral->getImaStatus()));
        $formMineral->handleRequest($request);
        $response = [];

        if($formMineral->isSubmitted() && $formMineral->isValid()) {
            $response = $this->mineralManager->setMineral(
                $formMineral["imgPath"]->getData(), 
                $mineral,
                $formMineral,
                $this->em
            );
        }

        return $this->render('admin/article/minerals/formMineral.html.twig', [
            "formMineral" => $formMineral->createView(),
            "response" => $response
        ]);
    }

    /**
     * Possibilité d'en faire une retour API
     * 
     * Attention : supprimer un minéral possèdant une liaison avec une autre table,
     * la donnée dans l'autre table et le minéral seront supprimés de la base de données.
     * 
     * @Route("/admin/mineral/{id}/delete", name="adminDeleteMineral")
     */
    public function admin_mineral_delete_by_id(int $id, Request $request)
    {
        $mineral = $this->em->getRepository(Mineral::class)->findOneBy(["id" => $id]);

        if(empty($mineral)) {
            throw new \Exception("The mineral with the id {$id} wasn't found.");
        }

        // Si le mineral possède une image
        if(!empty($mineral->getImgPath())) {
            unlink($this->getParameter('project_public_dir') . $mineral->getImgPath());
        }

        foreach($mineral->getCountries() as $oneCountry) {
            $mineral->removeCountry($oneCountry);
        }
        
        $this->em->remove($mineral);
        $this->em->flush();

        return $this->redirectToRoute('adminLivingThing');
    }

    /**
     * @Route("/admin/article", name="adminArticle")
     */
    public function admin_article(Request $request)
    {
        return $this->render('admin/article/index.html.twig');
    }

    /**
     * Affiche les articles selon la categorie d'appartenance. C'est-à-dire, on affiche 
     * les articles sur les êtres vivants si c'est la categorie demandée est les êtres vivants
     * 
     * @Route("/admin/article/{category}", name="adminArticleByCategory")
     */
    public function admin_article_by_category(string $category, Request $request)
    {
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $search = !empty($request->get('search')) ? $request->get('search') : null;
        $filterBy = !empty($request->get('filterBy')) ? $request->get('filterBy') : "all";
        $filterByChoices = ["all" => "All", "approved-article" => "Approved", "not-approuved-article" => "To Approuve"];
        $response = !empty($request->get('response')) ? $request->get('response') : [];
        $limit = 10;
        $nbrOffset = 1;

        if($category == "living-thing") {
            $nbrLivingThing = $this->em->getRepository(Article::class)->countArticleLivingThings();
            $nbrOffset = $nbrLivingThing > $limit ? ceil($nbrLivingThing / $limit) : $nbrOffset;

            return $this->render('admin/article/living-thing/listArticle.html.twig', [
                "articles" => $this->em->getRepository(Article::class)->getArticleLivingThings($offset, $limit),
                "nbrOffset" => $nbrOffset,
                "offset" => $offset,
                "category" => $category,
                "response" => $response
            ]);
        } elseif($category == "natural-elements") {
            $articleElements = $this->em->getRepository(Article::class)->getArticleElements($offset, $limit);
            $nbrElements = $this->em->getRepository(Article::class)->countArticleElements();
            $nbrOffset = $nbrElements > $limit ? ceil($nbrElements / $limit) : $nbrOffset;

            return $this->render('admin/article/natural-elements/listArticle.html.twig', [
                "articles" => $articleElements,
                "nbrOffset" => $nbrOffset,
                "offset" => $offset,
                "category" => $category,
                "response" => $response
            ]);
        } elseif($category == "minerals") {
            $articleMinerals = $this->em->getRepository(Article::class)->getArticleMinerals($offset, $limit);
            $nbrMinerals = $this->em->getRepository(Article::class)->countArticleMinerals();
            $nbrOffset = $nbrMinerals > $limit ? ceil($nbrMinerals / $limit) : $nbrOffset;

            return $this->render('admin/article/minerals/listArticle.html.twig', [
                "articles" => $articleMinerals,
                "nbrOffset" => $nbrOffset,
                "offset" => $offset,
                "category" => $category,
                "response" => $response
            ]);
        }

        return $this->redirectToRoute("adminArticle", [
            "response" => [
                "class" => "danger",
                "message" => "The category {$category} isn't allowed."
            ]
        ], 307);
    }

    /**
     * Ajout un article selon le type (la categorie => "living-thing" ou "natural-elements") de l'article.
     * 
     * @Route("/admin/article/{category}/add", name="adminAddArticleByCategory")
     */
    public function admin_add_article_by_category(string $category, Request $request)
    {
        $message = [];

        if($category == "living-thing") {
            $article = new ArticleLivingThing();
            $formArticle = $this->createForm(ArticleLivingThingType::class, $article);
            $formArticle->handleRequest($request);

            // Quand le formulaire est soumit et valide celon la config dans l'entity
            if($formArticle->isSubmitted() && $formArticle->isValid()) {
                $livingThing = $formArticle["livingThing"]->getData();
                
                $message = $this->livingThingManager->setLivingThing(
                    $formArticle["livingThing"]["imgPath"]->getData(),
                    $livingThing,
                    $this->em
                );

                // S'il n'y a pas eu d'erreur, alors ...
                if($message["error"] == false) {
                    $message = $this->articleLivingThingManager->setArticleLivingThing(
                        $article,
                        $livingThing,
                        $this->em,
                        $this->current_logged_user
                    );
                }
            }

            return $this->render('admin/article/living-thing/formArticle.html.twig', [
                "formArticle" => $formArticle->createView(),
                "category" => $category,
                "response" => $message
            ]);
        } elseif($category == "natural-elements") {
            $articleElement = new ArticleElement();
            $formArticle = $this->createForm(ArticleElementType::class, $articleElement);
            $formArticle->handleRequest($request);

            if($formArticle->isSubmitted() && $formArticle->isValid()) {
                $element = $formArticle["element"]->getData();

                $existingElement = $this->em->getRepository(Element::class)->getElementByName($element->getScientificName());
                
                if(empty($existingElement)) {
                    $message = $this->elementManager->setElement(
                        $formArticle["element"]["imgPath"]->getData(),
                        $element,
                        $this->em
                    );
                } else {
                    $element = $existingElement;
                }

                // S'il n'y a pas eu d'erreur rencontrée avec l'insertion de l'élément naturel
                if($message["error"] == false) {
                    if(\is_null($element->getArticleElement())) {
                        $message = $this->articleElementManager->setArticleElement(
                            $articleElement,
                            $element,
                            $this->em
                        );

                        $message = $this->articleManager->insertArticle(
                            $articleElement, 
                            $this->em, 
                            $this->current_logged_user
                        );
                    } else {
                        $message = [
                            "error" => true,
                            "class" => "danger",
                            "message" => "L'élément {$mineral->getName()} possède déjà un article. L'ajout du nouvel article est été annulé."
                        ];
                    }
                }
            }

            return $this->render('admin/article/natural-elements/formArticle.html.twig', [
                "formArticle" => $formArticle->createView(),
                "category" => $category,
                "response" => $message
            ]);
        } elseif($category == "minerals") {
            $articleMineral = new ArticleMineral();
            $formArticle = $this->createForm(ArticleMineralType::class, $articleMineral);
            $formArticle->handleRequest($request);

            if($formArticle->isSubmitted() && $formArticle->isValid()) {
                $mineral = $formArticle["mineral"]->getData();

                $existingMineral = $this->em->getRepository(Mineral::class)->getMineralByName($mineral->getName());
                if(empty($existingMineral)) {
                    $message = $this->mineralManager->setMineral(
                        $formArticle["mineral"]["imgPath"]->getData(),
                        $mineral,
                        $formArticle["mineral"],
                        $this->em
                    );
                } else {
                    $mineral = $existingMineral;
                }

                if(empty($message) || $message["error"] == false) {
                    if(\is_null($mineral->getArticleMineral())) {
                        $message = $this->articleMineralManager->setArticleMineral(
                            $articleMineral,
                            $mineral,
                            $this->em,
                            $this->current_logged_user
                        );
                    } else {
                        $message = [
                            "error" => true,
                            "class" => "danger",
                            "message" => "Le mineral {$mineral->getName()} possède déjà un article. L'ajout du nouvel article est annulé."
                        ];
                    }
                }

                if(empty($message) || $message["error"] == false) {
                    $message = $this->articleManager->insertArticle(
                        $articleMineral,
                        $this->em,
                        $this->current_logged_user
                    );
                }

                if(empty($message) || $message["error"] == false) {
                    $message = $this->mediaGalleryManager->setMediaGalleryMinerals(
                        $formArticle["mediaGallery"]->getData(),
                        $articleMineral,
                        $this->em
                    );
                }
            }

            return $this->render('admin/article/minerals/formArticle.html.twig', [
                "formArticle" => $formArticle->createView(),
                "category" => $category,
                "response" => $message
            ]);
        }

        return $this->redirectToRoute("adminArticle", [
            "class" => "danger",
            "message" => "The category {$category} isn't allowed."
        ], 307);
    }

    /**
     * @Route("/admin/article/{category}/{id}", name="adminSingleArticleByCategory")
     */
    public function admin_single_article_by_category(int $id, string $category)
    {
        if($category == "living-thing") {
            $article = $this->em->getRepository(Article::class)->getArticleLivingThing($id);

            if(empty($article)) {
                return $this->redirectToRoute("adminArticleByCategory", [
                    "category" => $category,
                    "class" => "danger",
                    "message" => "This article does not exist"
                ], 307);
            }

            return $this->render('admin/article/living-thing/detailArticle.html.twig', [
                "article" => $article,
                "category" => $category
            ]);
        } elseif($category == "natural-elements") {
            $article = $this->em->getRepository(Article::class)->getArticleElement($id);

            if(empty($article)) {
                return $this->redirectToRoute("adminArticleByCategory", [
                    "category" => $category,
                    "class" => "danger",
                    "message" => "This article does not exist"
                ], 307);
            }

            return $this->render('admin/article/natural-elements/detailArticle.html.twig', [
                "article" => $article,
                "category" => $category
            ]);
        } elseif($category == "minerals") {
            $article = $this->em->getRepository(Article::class)->getArticleMineral($id);

            if(empty($article)) {
                return $this->redirectToRoute("adminArticleByCategory", [
                    "category" => $category,
                    "class" => "danger",
                    "message" => "This article does not exist"
                ], 307);
            }

            return $this->render('admin/article/minerals/detailArticle.html.twig', [
                "article" => $article,
                "category" => $category
            ]);
        }

        return $this->redirectToRoute("adminArticle", [
            "class" => "danger",
            "message" => "The category {$category} isn't allowed."
        ], 307);
    }

    /**
     * @Route("/admin/article/{category}/{id}/edit", name="adminEditArticleByCategory")
     */
    public function admin_edit_article_by_category(int $id, string $category, Request $request)
    {
        $response = [];

        if($category == "living-thing") {
            $article = $this->em->getRepository(Article::class)->findOneBy(["id" => $id]);
            
            if(empty($article)) {
                return $this->redirectToRoute("adminArticle", [
                    "class" => "danger",
                    "message" => "This article does not exist."
                ], 307);
            }
            
            $articleLivingThing = $article->getArticleLivingThing();
            $formArticle = $this->createForm(ArticleLivingThingType::class, $articleLivingThing);
            $formArticle->get('livingThing')->setData($articleLivingThing->getIdLivingThing());
            $formArticle->handleRequest($request);

            if($formArticle->isSubmitted() && $formArticle->isValid()) {
                $response = $this->articleLivingThingManager->setArticleLivingThing(
                    $articleLivingThing,
                    $articleLivingThing->getIdLivingThing(),
                    $this->em
                );
            }

            return $this->render('admin/article/living-thing/formArticle.html.twig', [
                "formArticle" => $formArticle->createView(),
                "category" => $category,
                "response" => $response
            ]);
        } elseif($category == "natural-elements") {
            $article = $this->em->getRepository(Article::class)->findOneBy(["id" => $id]);

            if(empty($article)) {
                return $this->redirectToRoute("adminArticle", [
                    "class" => "danger",
                    "message" => "This article does not exist."
                ], 307);
            }
            
            $articleElement = $article->getArticleElement();
            $formArticle = $this->createForm(ArticleElementType::class, $articleElement);
            $formArticle->get('element')->setData($articleElement->getElement());
            $formArticle->handleRequest($request);

            if($formArticle->isSubmitted() && $formArticle->isValid()) {
                $response = $this->articleElementManager->setArticleElement(
                    $formArticle,
                    $formArticle->getElement(),
                    $this->em
                );

                if(!empty($response) && $response["error"] == false) {
                    $response = $this->articleManager->insertArticle(
                        $articleElement,
                        $formArticle["element"]->getData(),
                        $this->em,
                        $this->current_logged_user
                    );

                    if(!empty($response) && $response["error"] == false) {
                        $response = $this->mediaGalleryManager->setMediaGalleryElements(
                            $formArticle["mediaGallery"]->getData(),
                            $articleElement,
                            $this->em
                        );
                    }
                }
            }

            return $this->render('admin/article/natural-elements/formArticle.html.twig', [
                "formArticle" => $formArticle->createView(),
                "category" => $category,
                "response" => $response
            ]);
        } elseif($category == "minerals") {
            $article = $this->em->getRepository(Article::class)->findOneBy(["id" => $id]);

            if(empty($article)) {
                return $this->redirectToRoute("adminArticleByCategory", [
                    "category" => $category,
                    "response" => [
                        "class" => "danger",
                        "message" => "This article does not exist."
                    ]
                ], 307);
            }

            $articleMineral = $article->getArticleMineral();
            $mineral = $articleMineral->getMineral();
            $formArticle = $this->createForm(ArticleMineralType::class, $articleMineral);
            $formArticle->get('mineral')->setData($mineral);
            $formArticle->get('mineral')->get("imaStatus")->setData(implode(", ", $mineral->getImaStatus()));
            $formArticle->handleRequest($request);

            if($formArticle->isSubmitted() && $formArticle->isValid()) {
                $response = $this->mineralManager->setMineral(
                    $formArticle["mineral"]["imgPath"]->getData(),
                    $mineral,
                    $formArticle["mineral"],
                    $this->em
                );
                
                if(!empty($response) && $response["error"] == false) {
                    $response = $this->articleMineralManager->setArticleMineral(
                        $articleMineral,
                        $formArticle["mineral"]->getData(),
                        $this->em,
                        $this->current_logged_user
                    );

                    if(!empty($response) && $response["error"] == false) {
                        $response = $this->mediaGalleryManager->setMediaGalleryMinerals(
                            $formArticle["mediaGallery"]->getData(),
                            $articleMineral,
                            $this->em
                        );
                    }
                }
            }

            return $this->render('admin/article/minerals/formArticle.html.twig', [
                "formArticle" => $formArticle->createView(),
                "category" => $category,
                "response" => $response
            ]);
        }

        return $this->redirectToRoute("adminArticle", [
            "class" => "danger",
            "message" => "The category {$category} isn't allowed."
        ], 307);
    }

    /**
     * @Route("/admin/article/{category}/{id}/approve", name="adminApproveArticleByCategory")
     */
    public function admin_approve_single_article_by_category(int $id, string $category)
    {
        $article = null;
        $response = [];
        if($category == "living-thing") {
            $article = $this->em->getRepository(Article::class)->getArticleLivingThing($id);
        } elseif ($category == "natural-elements") {
            $article = $this->em->getRepository(Article::class)->getArticleElement($id);
        } elseif($category == "minerals") {
            $article = $this->em->getRepository(Article::class)->getArticleMineral($id);
        }

        if(empty($article)) {
            return $this->redirectToRoute("adminArticle", [
                "class" => "danger",
                "message" => "This article does not exist."
            ], 307);
        }

        if(!$article->getApproved()) {
            $notfication = new Notification();
            $notfication->setUser($article->getUser());
            $notfication->setType("success");
            $notfication->setContent("The content of the article {$article->getTitle()} you writed is accurate. This article is now public.");
            $notfication->setCreatedAt(new \DateTime());
            $article->setApproved(true);
            $this->em->persist($article);
            $this->em->persist($notfication);
            $this->em->flush();
        } else {
            $response = [
                "error" => true,
                "class" => "warning",
                "message" => "L'article {$article->getTitle()} a déjà été approuvé"
            ];
        }

        return $this->redirectToRoute("adminArticleByCategory", [
            "category" => $category
        ]);
    }

    /**
     * Possibilité d'en faire une response API
     * 
     * Supprimer un article uniquement. La liaison 1-1 avec un living thing que l'article possède
     * ne sera pas affectée
     * 
     * @Route("/admin/article/{category}/{id}/delete", name="adminDeleteArticleByCategory")
     */
    public function admin_delete_article_by_category(int $id, string $category)
    {
        $article = $this->em->getRepository(Article::class)->findOneBy(["id" => $id]);

        if(empty($article)) {
            return $this->redirectToRoute("adminArticleByCategory", [
                "category" => $category,
                "response" => [
                    "class" => "danger",
                    "message" => "This article does not exist."
                ]
            ], 307);
        }

        if($category == "living-thing") {
            $article->getArticleLivingThing()->setIdLivingThing(null);
        } elseif($category == "natural-elements") {
            $article->getArticleElement()->setElement(null);
        } elseif($category == "minerals") {
            $article->getArticleMineral()->setMineral(null);
        }

        // Envoi d'une notification à l'utilisateur
        $notfication = new Notification();
        $notfication->setUser($article->getUser());
        $notfication->setType("danger");
        $notfication->setContent("The content of the article {$article->getTitle()} you writed wasn't accurate. This article has been rejected.");
        $notfication->setCreatedAt(new \DateTime());
        $this->em->remove($article);
        $this->em->persist($notfication);
        $this->em->flush();

        return $this->redirectToRoute('adminArticleByCategory', [
            "category" => $category,
            "response" => [
                "class" => "success",
                "message" => "The article {$article->getTitle()} has been successfully deleted."
            ]
        ]);
    }

    /**
     * @Route("/admin/media", name="adminMedia")
     */
    public function admin_media(Request $request)
    {
        return $this->render('admin/media/index.html.twig');
    }

    /**
     * @Route("/admin/media/{type}", name="adminMediaType")
     */
    public function admin_media_by_type($type, Request $request)
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $medias = $this->em->getRepository(MediaGallery::class)->getMediaGalleryByType($type, $offset, $limit);
        $nbrOffset = ceil($this->em->getRepository(MediaGallery::class)->countMediaGalleryByType($type) / $limit);

        return $this->render('admin/media/list.html.twig', [
            "mediaType" => $type,
            "medias" => $medias,
            "offset" => $offset,
            "nbrOffset" => $nbrOffset,
        ]);
    }

    /**
     * @Route("/admin/media/{id}/delete", name="adminDeleteMediaByID", methods="DELETE")
     */
    public function admin_delete_media_by_id(int $id)
    {
        $media = $this->em->getRepository(MediaGallery::class)->getMediaGalleryByID($id);

        if(empty($media)) {
            return $this->json([
                "error" => true,
                "message" => "This media hasn't been found."
            ]);
        }

        // Suppression de la liaison existante avec le living thing
        if(!empty($media->getArticleLivingThing())) {
            $media->setArticleLivingThing(null);
        }

        // Suppression de la liaison existante avec le mineral
        if(!empty($media->getArticleMineral())) {
            $media->setArticleMineral(null);
        }

        // Suppression de la liaison existante avec l'élément chimique
        if(!empty($media->getArticleElement())) {
            $media->setArticleElement(null);
        }

        $this->em->remove($media);
        $this->em->flush();
        
        return $this->json([
            "error" => false,
            "class" => "success",
            "message" => "The media has been successfully deleted"
        ]);
    }

    /**
     * @Route("/admin/exports", name="adminExports")
     */
    public function admin_exports(Request $request)
    {
        return $this->render("admin/exports/index.html.twig", []);
    }
}
