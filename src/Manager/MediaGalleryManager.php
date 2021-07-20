<?php

namespace App\Manager;

use App\Entity\{MediaGallery, ArticleLivingThing, ArticleMineral, ArticleElement};
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MediaGalleryManager extends AbstractController {

    function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    /**
     * Insertion ou mise à jour des medias en base de données pour les articles "living thing".
     */
    public function setMediaGalleryLivingThing(array $files, ArticleLivingThing &$articleLivingThing, EntityManagerInterface $manager)
    {
        $date = new \DateTime();

        if(empty($files)) {
            return null;
        }

        foreach($files as $key => $oneFile) {
            $fileName = "{$articleLivingThing->getTitle()}_" . ($key + 1);
            $newFilename = "{$fileName}.{$oneFile->guessExtension()}";
            $kingdomDirectory = $this->getParameter('project_living_thing_media_gallery_dir') . $this->convertUppercaseToUcfirst($articleLivingThing->getIdLivingThing()->getKingdom());

            if(!\file_exists($kingdomDirectory)) {
                mkdir($kingdomDirectory, 0777, true);
            }

            try {
                if(
                    array_search(
                        $kingdomDirectory . $newFilename, 
                        glob($kingdomDirectory . "*." . $oneFile->guessExtension())
                    )
                ) {
                    unlink($kingdomDirectory . $newFilename);
                }
                
                $oneFile->move(
                    $kingdomDirectory,
                    $newFilename
                );
            } catch (FileException $e) {
                dd($e->getMessage());
            }

            if(empty($manager->getRepository(MediaGallery::class)->getMediaGalleryByName($fileName))) {
                $mediaGallery = new MediaGallery();
                $mediaGallery->setName($fileName);
                $mediaGallery->setPath("content/wikiearth/living-thing/{$this->convertUppercaseToUcfirst($articleLivingThing->getIdLivingThing()->getKingdom())}/{$newFilename}");
                $mediaGallery->setMediaType("image");
                $mediaGallery->setCreatedAt($date);
                $manager->persist($mediaGallery);
                $manager->flush();
                $manager->clear();
                
                $mediaGallery->setArticleLivingThing($articleLivingThing);
                $manager->merge($mediaGallery);
                $manager->flush();
            }
        }
    }

    public function setMediaGalleryElements(array $files, ArticleElement &$articleElement, EntityManagerInterface $manager)
    {
        // TODO : effectué la logique métier d'insertion des médias pour les articles de type "elements"
        $date = new \DateTime();

        if(empty($files)) {
            return null;
        }

        foreach($files as $key => $oneFile) {
            $fileName = "{$articleElement->getElement()->getName()}_" . ($key + 1);
            $newFilename = "{$fileName}.{$oneFile->guessExtension()}";
            $elementDirectory = $this->getParameter('project_natural_elements_elements_dir');

            if(!\file_exists($elementDirectory)) {
                mkdir($elementDirectory, 0777, true);
            }

            try {
                // If a file with the same name already exist, delete it
                if(
                    array_search(
                        $elementDirectory . $newFilename, 
                        glob($elementDirectory . "*." . $oneFile->guessExtension())
                    )
                ) {
                    unlink($elementDirectory . $newFilename);
                }
                
                // Move the file to the article directory
                $oneFile->move(
                    $elementDirectory,
                    $newFilename
                );
            } catch (FileException $e) {
                dd($e->getMessage());
            }

            if(empty($manager->getRepository(MediaGallery::class)->getMediaGalleryByName($fileName))) {
                $mediaGallery = new MediaGallery();
                $mediaGallery->setName($fileName);
                $mediaGallery->setPath("content/wikiearth/natural-elements/elements/{$newFilename}");
                $mediaGallery->setMediaType("image");
                $mediaGallery->setCreatedAt($date);
                $manager->persist($mediaGallery);
                $manager->flush();
                $manager->clear();
                
                $mediaGallery->setArticleElement($articleElement);
                $manager->merge($mediaGallery);
                $manager->flush();
            }
        }
    }

    public function setMediaGalleryMinerals(array $files, ArticleMineral &$articleMineral, EntityManagerInterface $manager)
    {
        $date = new \DateTime();

        if(empty($files)) {
            return null;
        }

        foreach($files as $key => $oneFile) {
            $fileName = "{$articleMineral->getMineral()->getName()}_" . ($key + 1);
            $newFilename = "{$fileName}.{$oneFile->guessExtension()}";
            $mineralDirectory = $this->getParameter('project_natural_elements_minerals_dir');

            if(!\file_exists($mineralDirectory)) {
                mkdir($mineralDirectory, 0777, true);
            }

            try {
                // If a file with the same name already exist, delete it
                if(
                    array_search(
                        $mineralDirectory . $newFilename, 
                        glob($mineralDirectory . "*." . $oneFile->guessExtension())
                    )
                ) {
                    unlink($mineralDirectory . $newFilename);
                }
                
                // Move the file to the article directory
                $oneFile->move(
                    $mineralDirectory,
                    $newFilename
                );
            } catch (FileException $e) {
                dd($e->getMessage());
            }

            if(empty($manager->getRepository(MediaGallery::class)->getMediaGalleryByName($fileName))) {
                $mediaGallery = new MediaGallery();
                $mediaGallery->setName($fileName);
                $mediaGallery->setPath("content/wikiearth/natural-elements/minerals/{$newFilename}");
                $mediaGallery->setMediaType("image");
                $mediaGallery->setCreatedAt($date);
                $manager->persist($mediaGallery);
                $manager->flush();
                $manager->clear();
                
                $mediaGallery->setArticleMineral($articleMineral);
                $manager->merge($mediaGallery);
                $manager->flush();
            }
        }
    }

    public function convertUppercaseToUcfirst(string $value)
    {
        return ucfirst(strtolower($value));
    }
}