<?php

namespace App\Controller;

use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Manager\{UserManager, LivingThingManager, ElementManager, MineralManager, ArticleLivingThingManager, ArticleElementManager, ArticleMineralManager};
use App\Form\{UserType, LivingThingType0, MineralType, ElementType, UserRegisterType, ArticleLivingThingType, ArticleElementType, ArticleMineralType};
use App\Entity\{User, Element, Mineral, SourceLink, LivingThing, MediaGallery, Notification, ArticleLivingThing, ArticleElement, ArticleMineral};

class AdminController extends AbstractController
{
    private $current_logged_user;
    private $livingThingManager;
    private $elementManager;
    private $mineralManager;
    private $articleLivingThingManager;
    private $articleElementManager;
    private $articleMineralManager;
    private $userManager;
    private $em;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $em, ContainerInterface $container)
    {
        $this->current_logged_user = $tokenStorage->getToken()->getUser();
        $this->livingThingManager = new LivingThingManager($container);
        $this->elementManager = new ElementManager($container);
        $this->mineralManager = new MineralManager($container);
        $this->articleLivingThingManager = new ArticleLivingThingManager();
        $this->articleElementManager = new ArticleElementManager();
        $this->articleMineralManager = new ArticleMineralManager();
        $this->userManager = new UserManager();
        $this->em = $em;
    }
    
    /**
     * @Route("/admin", name="adminHome")
     */
    public function admin_home()
    {
        return $this->render('admin/home/index.html.twig', [
            "nbrUsers" => $this->em->getRepository(User::class)->countUsers($this->current_logged_user->getId()),
            "nbrArticles" => $this->em->getRepository(ArticleLivingThing::class)->countArticleLivingThings(),
            "nbrLivingThings" => $this->em->getRepository(LivingThing::class)->countLivingThings()
        ]);
    }

    /**
     * @Route("/admin/profile", name="adminProfile")
     */
    public function admin_profile(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $formUser = $this->createForm(UserType::class, $this->current_logged_user);
        $formUser->handleRequest($request);

        if($formUser->isSubmitted() && $formUser->isValid()) {
            $this->userManager->updateUser(
                $formUser, 
                $this->current_logged_user, 
                $this->em, 
                $encoder, 
                $this->getParameter('project_users_dir')
            );
        }

        return $this->render('admin/profile/index.html.twig', [
            "userForm" => $formUser->createView(),
            "userImg" => $this->current_logged_user->getImgPath() ? $this->current_logged_user->getImgPath() : "https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1024px-User_icon_2.svg.png"
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

        return $this->render('admin/users/index.html.twig', [
            "users" => $this->em->getRepository(User::class)->getUsers($offset - 1, $limit, $this->current_logged_user->getId()),
            "offset" => $offset,
            "total_page" => $nbrOffset
        ]);
    }

    /**
     * @Route("/admin/users/{id}", name="adminUserEdit")
     */
    public function admin_user_edit(User $user, Request $request, UserPasswordEncoderInterface $encoder)
    {
        $formUser = $this->createForm(UserType::class, $user);
        $formUser->handleRequest($request);

        if($formUser->isSubmitted() && $formUser->isValid()) {
            $this->userManager->updateUser(
                $formUser, 
                $user, 
                $this->em, 
                $encoder, 
                $this->getParameter('project_users_dir')
            );

            $this->redirectToRoute('adminUsersListing');
        }

        return $this->render('admin/users/edit.html.twig', [
            "userForm" => $formUser->createView(),
            "userImg" => $user->getImgPath() ? $user->getImgPath() : "https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1024px-User_icon_2.svg.png"
        ]);
    }
    
    /**
     * @Route("/admin/users/{id}/delete", name="adminUserDelete")
     */
    public function admin_user_delete(User $user)
    {
        $this->em->remove($user);
        $this->em->flush();

        return $this->redirectToRoute('adminUsersListing');
    }

    /**
     * @Route("/admin/living-thing", name="adminLivingThing")
     */
    public function admin_living_thing(Request $request)
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $nbrLivingThing = $this->em->getRepository(LivingThing::class)->countLivingThings();
        $nbrOffset = $nbrLivingThing > $limit ? ceil($nbrLivingThing / $limit) : 1;

        return $this->render('admin/living_thing/index.html.twig', [
            "livingThings" => $this->em->getRepository(LivingThing::class)->getLivingThings($offset, $limit),
            "offset" => $offset,
            "nbrOffset" => $nbrOffset
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
        $message = "";

        if($formLivingThing->isSubmitted() && $formLivingThing->isValid()) {
            $message = $this->livingThingManager->setLivingThing(
                $formLivingThing["imgPath"]->getData(), 
                $livingThing, 
                $this->em
            );
        }

        return $this->render('admin/living_thing/edit.html.twig', [
            "formLivingThing" => $formLivingThing->createView(),
            "response" => $message
        ]);
    }

    /**
     * @Route("/admin/living-thing/{id}/article", name="adminLivingThingCreateArticle")
     */
    public function admin_living_thing_create_article($id, Request $request)
    {
        $articleLivingThing = $this->em->getRepository(ArticleLivingThing::class)->findOneBy(["id" => $id]);
        $message = [];

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
                return $this->redirectToRoute("404Error");
            }
        } else {
            return $this->redirectToRoute("404Error");
        }

        return $this->render('admin/article/living-thing/new.html.twig', [
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
                $formLivingThing, 
                $livingThing, 
                $this->em
            );
        }

        return $this->render('admin/living_thing/edit.html.twig', [
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
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $limit = 10;

        return $this->render('admin/natural_element/element/index.html.twig', [
            "offset" => $offset,
            "nbrOffset" => ceil($this->em->getRepository(Element::class)->countElements() / $limit),
            "elements" => $this->em->getRepository(Element::class)->getElements($offset, $limit),
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
                    $this->em
                );
            } else {
                $response = [
                    "class" => "danger",
                    "message" => "The element {$element->getScientificName()} already exist in the databse."
                ];
            }
        }
        return $this->render('admin/natural_element/element/form.html.twig', [
            "formElement" => $formElement->createView(),
            "response" => $response
        ]);
    }

    /**
     * @Route("/admin/mineral", name="adminMineral")
     */
    public function admin_mineral(Request $request)
    {
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $limit = 10;

        return $this->render('admin/natural_element/mineral/index.html.twig', [
            "offset" => $offset,
            "nbrOffset" => ceil($this->em->getRepository(Mineral::class)->countMinerals() / $limit),
            "minerals" => $this->em->getRepository(Mineral::class)->getMinerals($offset, $limit),
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
        return $this->render('admin/natural_element/mineral/form.html.twig', [
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

        return $this->render('admin/natural_element/mineral/form.html.twig', [
            "formMineral" => $formMineral->createView(),
            "response" => $response
        ]);
    }

    /**
     * Possibilité d'en faire une retour API
     * 
     * Attention : supprimer un living thing possèdant une liaison avec une autre table,
     * la donnée dans l'autre table et le living thing seront supprimés de la base de données.
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
        $limit = 10;
        $nbrOffset = 1;

        if($category == "living-thing") {
            $nbrLivingThing = $this->em->getRepository(ArticleLivingThing::class)->countArticleLivingThings();
            $nbrOffset = $nbrLivingThing > $limit ? ceil($nbrLivingThing / $limit) : $nbrOffset;

            return $this->render('admin/article/living-thing/index.html.twig', [
                "articles" => $this->em->getRepository(ArticleLivingThing::class)->getArticleLivingThings($offset, $limit),
                "nbrOffset" => $nbrOffset,
                "offset" => $offset,
                "category" => $category
            ]);
        } elseif($category == "natural-elements") {
            $nbrElements = $this->em->getRepository(ArticleElement::class)->countArticleElements();
            $nbrOffset = $nbrElements > $limit ? ceil($nbrElements / $limit) : $nbrOffset;

            return $this->render('admin/article/natural-elements/index.html.twig', [
                "articles" => $this->em->getRepository(ArticleElement::class)->getArticleElements($offset, $limit),
                "nbrOffset" => $nbrOffset,
                "offset" => $offset,
                "category" => $category
            ]);
        } elseif($category == "minerals") {
            $nbrMinerals = $this->em->getRepository(ArticleMineral::class)->countArticleMinerals();
            $nbrOffset = $nbrMinerals > $limit ? ceil($nbrMinerals / $limit) : $nbrOffset;

            return $this->render('admin/article/minerals/index.html.twig', [
                "articles" => $this->em->getRepository(ArticleMineral::class)->getArticleMinerals($offset, $limit),
                "nbrOffset" => $nbrOffset,
                "offset" => $offset,
                "category" => $category
            ]);
        }

        return $this->redirectToRoute("404Error");
    }

    /**
     * Ajout un article celon le type (la categorie => "living-thing" ou "natural-elements") de l'article.
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

            return $this->render('admin/article/living-thing/edit.html.twig', [
                "formArticle" => $formArticle->createView(),
                "category" => $category,
                "response" => $message
            ]);
        } elseif($category == "natural-elements") {
            $article = new ArticleElement();
            $formArticle = $this->createForm(ArticleElementType::class, $article);
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
                            $article,
                            $element,
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

            return $this->render('admin/article/natural-elements/edit.html.twig', [
                "formArticle" => $formArticle->createView(),
                "category" => $category,
                "response" => $message
            ]);
        } elseif($category == "minerals") {
            $article = new ArticleMineral();
            $formArticle = $this->createForm(ArticleMineralType::class, $article);
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
                            $article,
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
            }

            return $this->render('admin/article/minerals/edit.html.twig', [
                "formArticle" => $formArticle->createView(),
                "category" => $category,
                "response" => $message
            ]);
        }

        return $this->redirectToRoute("404Error");
    }

    /**
     * @Route("/admin/article/{category}/{id}", name="adminSingleArticleByCategory")
     */
    public function admin_single_article_by_category(int $id, string $category)
    {
        if($category == "living-thing") {
            return $this->render('admin/article/living-thing/details.html.twig', [
                "article" => $this->em->getRepository(ArticleLivingThing::class)->findOneBy(["id" => $id]),
                "category" => $category
            ]);
        } elseif($category == "natural-elements") {
            return $this->render('admin/article/natural-elements/details.html.twig', [
                "article" => $this->em->getRepository(ArticleElement::class)->findOneBy(["id" => $id]),
                "category" => $category
            ]);
        } elseif($category == "minerals") {
            return $this->render('admin/article/minerals/details.html.twig', [
                "article" => $this->em->getRepository(ArticleMineral::class)->findOneBy(["id" => $id]),
                "category" => $category
            ]);
        }

        return $this->redirectToRoute("404Error");
    }

    /**
     * @Route("/admin/article/{category}/{id}/approve", name="adminApproveArticleByCategory")
     */
    public function admin_approve_single_article_by_category(int $id, string $category)
    {
        $article = null;
        if($category == "living-thing") {
            $article = $this->em->getRepository(ArticleLivingThing::class)->findOneBy(["id" => $id]);
        } elseif ($category == "natural-elements") {
            $article = $this->em->getRepository(ArticleElement::class)->findOneBy(["id" => $id]);
        } elseif($categoy == "minerals") {
            $article = $this->em->getRepository(ArticleMineral::class)->findOneBy(["id" => $id]);
        }

        if(empty($article)) {
            return $this->redirectToRoute("404Error");
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
        }

        return $this->redirectToRoute("adminArticleByCategory", [
            "category" => $category
        ]);
    }

    /**
     * @Route("/admin/article/{category}/{id}/edit", name="adminEditArticleByCategory")
     */
    public function admin_edit_article_by_category(int $id, string $category, Request $request)
    {
        if($category == "living-thing") {
            $articleLivingThing = $this->em->getRepository(ArticleLivingThing::class)->findOneBy(["id" => $id]);
            
            if(empty($articleLivingThing)) {
                return $this->redirectToRoute("404Error");
            }
            
            $formArticle = $this->createForm(ArticleLivingThingType::class, $articleLivingThing);
            $formArticle->get('livingThing')->setData($articleLivingThing->getIdLivingThing());
            $formArticle->handleRequest($request);

            if($formArticle->isSubmitted() && $formArticle->isValid()) {
                $this->articleLivingThingManager->setArticleLivingThing(
                    $articleLivingThing,
                    $articleLivingThing->getIdLivingThing(),
                    $this->em
                );
            }

            return $this->render('admin/article/living-thing/edit.html.twig', [
                "formArticle" => $formArticle->createView(),
                "category" => $category
            ]);
        } elseif($category == "natural-elements") {
            $articleElement = $this->em->getRepository(ArticleElement::class)->findOneBy(["id" => $id]);

            if(empty($articleElement)) {
                return $this->redirectToRoute("404Error");
            }

            $formArticle = $this->createForm(ArticleElementType::class, $articleElement);
            $formArticle->get('element')->setData($articleElement->getElement());
            $formArticle->handleRequest($request);

            if($formArticle->isSubmitted() && $formArticle->isValid()) {
                $this->articleElementManager->setArticleElement(
                    $formArticle,
                    $formArticle->getElement(),
                    $this->em,
                    $this->current_logged_user
                );
            }

            return $this->render('admin/article/natural-elements/edit.html.twig', [
                "formArticle" => $formArticle->createView(),
                "category" => $category
            ]);
        } elseif($category == "minerals") {
            $articleMineral = $this->em->getRepository(ArticleMineral::class)->findOneBy(["id" => $id]);

            if(empty($articleMineral)) {
                return $this->redirectToRoute("404Error");
            }

            $formArticle = $this->createForm(ArticleMineralType::class, $articleMineral);
            $formArticle->get('mineral')->setData($articleMineral->getMineral());
            $formArticle->handleRequest($request);

            if($formArticle->isSubmitted() && $formArticle->isValid()) {
                $this->articleElementManager->setArticleMineral(
                    $formArticle,
                    $formArticle->getMineral(),
                    $this->em,
                    $this->current_logged_user
                );
            }

            return $this->render('admin/article/minerals/edit.html.twig', [
                "formArticle" => $formArticle->createView(),
                "category" => $category
            ]);
        }

        return $this->redirectToRoute("404Error");
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
        $article = null;
        if($category == "living-thing") {
            $article = $this->em->getRepository(ArticleLivingThing::class)->findOneBy(["id" => $id]);
        } elseif($category == "natural-elements") {
            $article = $this->em->getRepository(ArticleElement::class)->findOneBy(["id" => $id]);
        } elseif($categoty == "minerals") {
            $article = $this->em->getRepository(ArticleMineral::class)->findOneBy(["id" => $id]);
        }

        if(empty($article)) {
            return $this->redirectToRoute("404Error");
        }

        if($category == "living-thing") {
            $article->setIdLivingThing(null);
        } elseif($category == "natural-elements") {
            $article->setElement(null);
        } elseif($category == "minerals") {
            $article->setMineral(null);
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
            "category" => $category
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

        return $this->render('admin/media/list.html.twig', [
            "mediaType" => $type,
            "medias" => $this->em->getRepository(MediaGallery::class)->getMediaGalleryByType($type, $offset, $limit)
        ]);
    }
}
