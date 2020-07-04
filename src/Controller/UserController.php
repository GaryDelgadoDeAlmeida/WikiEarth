<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Form\LivingThingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
     * @Route("/user/animal/add", name="userAddAnimal")
     */
    public function user_add_animal(Request $request, EntityManagerInterface $manager)
    {
        $animal = new Animal();
        $formAnimal = $this->createForm(LivingThingType::class, $animal);
        $formAnimal->handleRequest($request);

        if($formAnimal->isSubmitted() && $formAnimal->isValid()) {
            $mediaFile = $formAnimal['imgPath']->getData();
            if($mediaFile) {
                $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $newFilename = $animal->getName().'.'.$mediaFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    if(
                        array_search(
                            $this->getParameter('project_wikiearth_dir').$animal->getKingdom()."/img/".$animal->getName()."/".$newFilename, 
                            glob($this->getParameter('project_wikiearth_dir').$animal->getKingdom()."/img/".$animal->getName()."/*.".$mediaFile->guessExtension())
                        )
                    ) {
                        unlink($this->getParameter('project_wikiearth_dir').$animal->getKingdom()."/img/".$animal->getName()."/".$newFilename);
                    }
                    
                    $mediaFile->move(
                        $this->getParameter('project_wikiearth_dir').$animal->getKingdom()."/img/".$animal->getName(),
                        $newFilename
                    );
                } catch (FileException $e) {
                    dd($e->getMessage());
                }

                $animal->setImgPath($this->getParameter('project_wikiearth_dir').$animal->getKingdom()."/img/".$animal->getName()."/".$newFilename);
            }

            $manager->persist($animal);
            $manager->flush();
        }

        return $this->render('user/animal/edit.html.twig', [
            "formAnimal" => $formAnimal->createView()
        ]);
    }

    /**
     * @Route("/user/animal/{id}/edit", name="userEditAnimal")
     */
    public function user_edit_animal(Animal $animal, Request $request, EntityManagerInterface $manager)
    {
        $formAnimal = $this->createForm(LivingThingType::class, $animal);
        $formAnimal->handleRequest($request);

        if($formAnimal->isSubmitted() && $formAnimal->isValid()) {
            $mediaFile = $formAnimal['imgPath']->getData();
            if($mediaFile) {
                $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $newFilename = $animal->getName() .'.'.$mediaFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    if(array_search('./content/wikiearth/animal/img/'.$animal->getName().'/'.$newFilename, glob("./content/wikiearth/animal/img/".$animal->getName()."/*.".$mediaFile->guessExtension()))) {
                        unlink('./content/wikiearth/animal/img/'.$animal->getName().'/'.$newFilename);
                    }
                    
                    $mediaFile->move(
                        $this->getParameter('photo_animal_img_dir') . '/' . $animal->getName(),
                        $newFilename
                    );
                } catch (FileException $e) {
                    dd($e->getMessage());
                }

                $animal->setImgPath('/content/wikiearth/animal/img/'.$animal->getName().'/'.$newFilename);
            }

            $manager->persist($animal);
            $manager->flush();
        }

        return $this->render('user/animal/edit.html.twig', [
            "formAnimal" => $formAnimal->createView()
        ]);
    }

    /**
     * @Route("/user/article", name="userArticle")
     */
    public function user_article()
    {
        return $this->render('user/article/index.html.twig');
    }

    /**
     * @Route("/user/article/add", name="userArticleAdd")
     */
    public function user_article_add()
    {
        return $this->render('user/article/add.html.twig');
    }
}
