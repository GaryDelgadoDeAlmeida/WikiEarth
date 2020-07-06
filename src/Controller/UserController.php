<?php

namespace App\Controller;

use App\Form\UserType;
use App\Entity\LivingThing;
use App\Form\LivingThingType;
use App\Entity\ArticleLivingThing;
use App\Form\ArticleLivingThingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{   
    /**
     * @Route("/user", name="userHome")
     */
    public function user_home()
    {
        return $this->render('user/home/index.html.twig');
    }

    /**
     * @Route("/user/profile", name="userProfile")
     */
    public function user_profil(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $formUser = $this->createForm(UserType::class, $user);
        $formUser->handleRequest($request);

        if($formUser->isSubmitted() && $formUser->isValid()) {
            $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
            $manager->persist($user);
            $manager->flush();
        }
        
        return $this->render('user/profile/index.html.twig');
    }

    /**
     * @Route("/user/living-thing", name="userLivingThing")
     */
    public function user_living_thing()
    {
        return $this->render('user/living_thing/index.html.twig', [
            "livingThings" => []
        ]);
    }

    /**
     * @Route("/user/living-thing/add", name="userAddLivingThing")
     */
    public function user_add_living_thing(Request $request, EntityManagerInterface $manager)
    {
        $livingThing = new LivingThing();
        $formAnimal = $this->createForm(LivingThingType::class, $livingThing);
        $formAnimal->handleRequest($request);

        if($formAnimal->isSubmitted() && $formAnimal->isValid()) {
            $mediaFile = $formAnimal['imgPath']->getData();
            if($mediaFile) {
                $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $newFilename = $livingThing->getName().'.'.$mediaFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    if(
                        array_search(
                            $this->getParameter('project_wikiearth_dir').$livingThing->getKingdom()."/img/".$livingThing->getName()."/".$newFilename, 
                            glob($this->getParameter('project_wikiearth_dir').$livingThing->getKingdom()."/img/".$livingThing->getName()."/*.".$mediaFile->guessExtension())
                        )
                    ) {
                        unlink($this->getParameter('project_wikiearth_dir').$livingThing->getKingdom()."/img/".$livingThing->getName()."/".$newFilename);
                    }
                    
                    $mediaFile->move(
                        $this->getParameter('project_wikiearth_dir').$livingThing->getKingdom()."/img/".$livingThing->getName(),
                        $newFilename
                    );
                } catch (FileException $e) {
                    dd($e->getMessage());
                }

                $livingThing->setImgPath($this->getParameter('project_wikiearth_dir').$livingThing->getKingdom()."/img/".$livingThing->getName()."/".$newFilename);
            }

            $manager->persist($livingThing);
            $manager->flush();
        }

        return $this->render('user/living_thing/edit.html.twig', [
            "formAnimal" => $formAnimal->createView()
        ]);
    }

    /**
     * @Route("/user/living-thing/{id}/edit", name="userEditLivingThing")
     */
    public function user_edit_living_thing(LivingThing $livingThing, Request $request, EntityManagerInterface $manager)
    {
        $formAnimal = $this->createForm(LivingThingType::class, $livingThing);
        $formAnimal->handleRequest($request);

        if($formAnimal->isSubmitted() && $formAnimal->isValid()) {
            $mediaFile = $formAnimal['imgPath']->getData();
            if($mediaFile) {
                $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $newFilename = $livingThing->getName() .'.'.$mediaFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    if(array_search('./content/wikiearth/animal/img/'.$livingThing->getName().'/'.$newFilename, glob("./content/wikiearth/animal/img/".$livingThing->getName()."/*.".$mediaFile->guessExtension()))) {
                        unlink('./content/wikiearth/animal/img/'.$livingThing->getName().'/'.$newFilename);
                    }
                    
                    $mediaFile->move(
                        $this->getParameter('photo_animal_img_dir') . '/' . $livingThing->getName(),
                        $newFilename
                    );
                } catch (FileException $e) {
                    dd($e->getMessage());
                }

                $livingThing->setImgPath('/content/wikiearth/animal/img/'.$livingThing->getName().'/'.$newFilename);
            }

            $manager->persist($livingThing);
            $manager->flush();
        }

        return $this->render('user/living_thing/edit.html.twig', [
            "formAnimal" => $formAnimal->createView()
        ]);
    }

    /**
     * @Route("/user/article", name="userArticle")
     */
    public function user_article()
    {
        return $this->render('user/article/index.html.twig', [
            "articles" => $this->get('security.token_storage')->getToken()->getUser()->getArticleLivingThings()
        ]);
    }

    /**
     * @Route("/user/article/add", name="userAddArticle")
     */
    public function user_add_article(Request $request, EntityManagerInterface $manager)
    {
        $article = new ArticleLivingThing();
        $formArticle = $this->createForm(ArticleLivingThingType::class, $article);
        $formArticle->handleRequest($request);

        if($formArticle->isSubmitted() && $formArticle->isValid()) {
            $mediaLivingThingFile = isset($request->files->get('article_living_thing')['livingThing']['imgPath']) ? $request->files->get('article_living_thing')['livingThing']['imgPath'] : null;
            $formRequest = $request->get('article_living_thing');
            
            $livingThing = new LivingThing();
            $livingThing->setCommonName($formRequest["livingThing"]['commonName']);
            $livingThing->setName($formRequest["livingThing"]['name']);
            $livingThing->setKingdom($formRequest["livingThing"]['kingdom']);
            $livingThing->setSubKingdom($formRequest["livingThing"]['subKingdom']);
            $livingThing->setDomain($formRequest["livingThing"]['domain']);
            $livingThing->setBranch($formRequest["livingThing"]['branch']);
            $livingThing->setSubBranch($formRequest["livingThing"]['subBranch']);
            $livingThing->setInfraBranch($formRequest["livingThing"]['infraBranch']);
            $livingThing->setDivision($formRequest["livingThing"]['division']);
            $livingThing->setSuperClass($formRequest["livingThing"]['superClass']);
            $livingThing->setClass($formRequest["livingThing"]['class']);
            $livingThing->setSubClass($formRequest["livingThing"]['subClass']);
            $livingThing->setInfraClass($formRequest["livingThing"]['infraClass']);
            $livingThing->setSuperOrder($formRequest["livingThing"]['superOrder']);
            $livingThing->setNormalOrder($formRequest["livingThing"]['normalOrder']);
            $livingThing->setSubOrder($formRequest["livingThing"]['subOrder']);
            $livingThing->setInfraOrder($formRequest["livingThing"]['infraOrder']);
            $livingThing->setMicroOrder($formRequest["livingThing"]['microOrder']);
            $livingThing->setSuperFamily($formRequest["livingThing"]['superFamily']);
            $livingThing->setFamily($formRequest["livingThing"]['family']);
            $livingThing->setSubFamily($formRequest["livingThing"]['subFamily']);
            $livingThing->setGenus($formRequest["livingThing"]['genus']);
            $livingThing->setSubGenus($formRequest["livingThing"]['subGenus']);
            $livingThing->setSpecies($formRequest["livingThing"]['species']);
            $livingThing->setSubSpecies($formRequest["livingThing"]['subSpecies']);
            
            if($mediaLivingThingFile != null) {
                $originalFilename = pathinfo($mediaLivingThingFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $newFilename = $livingThing->getName().'.'.$mediaLivingThingFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    if(
                        array_search(
                            $this->getParameter('project_wikiearth_dir').$livingThing->getKingdom()."/img/".$livingThing->getName()."/".$newFilename, 
                            glob($this->getParameter('project_wikiearth_dir').$livingThing->getKingdom()."/img/".$livingThing->getName()."/*.".$mediaLivingThingFile->guessExtension())
                        )
                    ) {
                        unlink($this->getParameter('project_wikiearth_dir').$livingThing->getKingdom()."/img/".$livingThing->getName()."/".$newFilename);
                    }
                    
                    $mediaLivingThingFile->move(
                        $this->getParameter('project_wikiearth_dir').$livingThing->getKingdom()."/img/".$livingThing->getName(),
                        $newFilename
                    );
                    
                    $livingThing->setImgPath($this->getParameter('project_wikiearth_dir').$livingThing->getKingdom()."/img/".$livingThing->getName()."/".$newFilename);
                } catch (FileException $e) {
                    die($e->getMessage());
                }
            }
            
            $article = new ArticleLivingThing();
            $article->setUser($this->get('security.token_storage')->getToken()->getUser());
            $article->setIdLivingThing($livingThing);
            $article->setTitle($formRequest['title']);
            $article->setGeography([
                "subTitle_1" => $formRequest['geography_sub_title'],
                "subContent_1" => $formRequest['geography_sub_content']
            ]);
            $article->setEcology([
                "subTitle_1" => $formRequest['ecology_sub_title'],
                "subContent_1" => $formRequest['ecology_sub_content']
            ]);
            $article->setBehaviour([
                "subTitle_1" => $formRequest['behaviour_sub_title'],
                "subContent_1" => $formRequest['behaviour_sub_content']
            ]);
            $article->setWayOfLife([
                "subTitle_1" => $formRequest['wayOfLife_sub_title'],
                "subContent_1" => $formRequest['wayOfLife_sub_content']
            ]);
            $article->setDescription([
                "subTitle_1" => $formRequest['description_sub_title'],
                "subContent_1" => $formRequest['description_sub_content']
            ]);
            $article->setOtherData([
                "subTitle_1" => $formRequest['otherData_sub_title'],
                "subContent_1" => $formRequest['otherData_sub_content']
            ]);
            $article->setApproved(false);
            $article->setCreatedAt(new \DateTime());

            $manager->persist($livingThing);
            $manager->persist($article);
            $manager->flush();
        }

        return $this->render('user/article/add.html.twig', [
            "formArticle" => $formArticle->createView()
        ]);
    }

    /**
     * @Route("/user/article/{id}/add", name="userEditArticle")
     */
    public function user_edit_article(ArticleLivingThing $article, Request $request, EntityManagerInterface $manager)
    {
        $formArticle = $this->createForm(ArticleLivingThingType::class, $article);
        $formArticle->handleRequest($request);

        if($formArticle->isSubmitted() && $formArticle->isValid()) {
            $mediaLivingThingFile = isset($request->files->get('article_living_thing')['livingThing']['imgPath']) ? $request->files->get('article_living_thing')['livingThing']['imgPath'] : null;
            $formRequest = $request->get('article_living_thing');
            
            $livingThing = new LivingThing();
            $livingThing->setCommonName($formRequest["livingThing"]['commonName']);
            $livingThing->setName($formRequest["livingThing"]['name']);
            $livingThing->setKingdom($formRequest["livingThing"]['kingdom']);
            $livingThing->setSubKingdom($formRequest["livingThing"]['subKingdom']);
            $livingThing->setDomain($formRequest["livingThing"]['domain']);
            $livingThing->setBranch($formRequest["livingThing"]['branch']);
            $livingThing->setSubBranch($formRequest["livingThing"]['subBranch']);
            $livingThing->setInfraBranch($formRequest["livingThing"]['infraBranch']);
            $livingThing->setDivision($formRequest["livingThing"]['division']);
            $livingThing->setSuperClass($formRequest["livingThing"]['superClass']);
            $livingThing->setClass($formRequest["livingThing"]['class']);
            $livingThing->setSubClass($formRequest["livingThing"]['subClass']);
            $livingThing->setInfraClass($formRequest["livingThing"]['infraClass']);
            $livingThing->setSuperOrder($formRequest["livingThing"]['superOrder']);
            $livingThing->setNormalOrder($formRequest["livingThing"]['normalOrder']);
            $livingThing->setSubOrder($formRequest["livingThing"]['subOrder']);
            $livingThing->setInfraOrder($formRequest["livingThing"]['infraOrder']);
            $livingThing->setMicroOrder($formRequest["livingThing"]['microOrder']);
            $livingThing->setSuperFamily($formRequest["livingThing"]['superFamily']);
            $livingThing->setFamily($formRequest["livingThing"]['family']);
            $livingThing->setSubFamily($formRequest["livingThing"]['subFamily']);
            $livingThing->setGenus($formRequest["livingThing"]['genus']);
            $livingThing->setSubGenus($formRequest["livingThing"]['subGenus']);
            $livingThing->setSpecies($formRequest["livingThing"]['species']);
            $livingThing->setSubSpecies($formRequest["livingThing"]['subSpecies']);
            
            if($mediaLivingThingFile != null) {
                $originalFilename = pathinfo($mediaLivingThingFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $newFilename = $livingThing->getName().'.'.$mediaLivingThingFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    if(
                        array_search(
                            $this->getParameter('project_wikiearth_dir').$livingThing->getKingdom()."/img/".$livingThing->getName()."/".$newFilename, 
                            glob($this->getParameter('project_wikiearth_dir').$livingThing->getKingdom()."/img/".$livingThing->getName()."/*.".$mediaLivingThingFile->guessExtension())
                        )
                    ) {
                        unlink($this->getParameter('project_wikiearth_dir').$livingThing->getKingdom()."/img/".$livingThing->getName()."/".$newFilename);
                    }
                    
                    $mediaLivingThingFile->move(
                        $this->getParameter('project_wikiearth_dir').$livingThing->getKingdom()."/img/".$livingThing->getName(),
                        $newFilename
                    );
                    
                    $livingThing->setImgPath($this->getParameter('project_wikiearth_dir').$livingThing->getKingdom()."/img/".$livingThing->getName()."/".$newFilename);
                } catch (FileException $e) {
                    die($e->getMessage());
                }
            }
            
            $article = new ArticleLivingThing();
            $article->setUser($this->get('security.token_storage')->getToken()->getUser());
            $article->setIdLivingThing($livingThing);
            $article->setTitle($formRequest['title']);
            $article->setGeography([
                "subTitle_1" => $formRequest['geography_sub_title'],
                "subContent_1" => $formRequest['geography_sub_content']
            ]);
            $article->setEcology([
                "subTitle_1" => $formRequest['ecology_sub_title'],
                "subContent_1" => $formRequest['ecology_sub_content']
            ]);
            $article->setBehaviour([
                "subTitle_1" => $formRequest['behaviour_sub_title'],
                "subContent_1" => $formRequest['behaviour_sub_content']
            ]);
            $article->setWayOfLife([
                "subTitle_1" => $formRequest['wayOfLife_sub_title'],
                "subContent_1" => $formRequest['wayOfLife_sub_content']
            ]);
            $article->setDescription([
                "subTitle_1" => $formRequest['description_sub_title'],
                "subContent_1" => $formRequest['description_sub_content']
            ]);
            $article->setOtherData([
                "subTitle_1" => $formRequest['otherData_sub_title'],
                "subContent_1" => $formRequest['otherData_sub_content']
            ]);
            $article->setApproved(false);
            $article->setCreatedAt(new \DateTime());

            $manager->persist($livingThing);
            $manager->persist($article);
            $manager->flush();
        }

        return $this->render('user/article/add.html.twig', [
            "formArticle" => $formArticle->createView()
        ]);
    }

    /**
     * @Route("/user/chat", name="userChat")
     */
    public function user_chat()
    {
        return $this->render('user/chat/index.html.twig');
    }
}
