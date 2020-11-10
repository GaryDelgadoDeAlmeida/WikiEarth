<?php

namespace App\Manager;

use App\Entity\LivingThing;
use App\Entity\MediaGallery;
use App\Entity\ArticleLivingThing;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MediaGalleryManager extends AbstractController {

    function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    /**
     * Insertion ou mise à jour des medias en base de données.
     */
    public function setMediaGalleryLivingThing(array $files, ArticleLivingThing &$articleLivingThing, EntityManagerInterface $manager)
    {
        $date = new \DateTime();
        $mediaGallery = null;

        foreach($files as $key => $oneFile) {
            $fileName = "{$articleLivingThing->getIdLivingThing()->getName()}_" . ($key + 1);
            $newFilename = "{$fileName}.{$oneFile->guessExtension()}";
            $kingdomDirectory = $this->getParameter('project_living_thing_media_gallery_dir') . $this->convertKingdomClassification(ucfirst(strtolower($articleLivingThing->getIdLivingThing()->getKingdom())));

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
                $mediaGallery->setPath("content/wikiearth/living-thing/media-gallery/{$this->convertKingdomClassification(ucfirst(strtolower($articleLivingThing->getIdLivingThing()->getKingdom())))}/{$newFilename}");
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

    public function setMediaGalleryAtomes(array $files)
    {
        // TODO : effectué la logique métier d'insertion des médias pour les articles de type "atomes"
    }

    public function setMediaGalleryMinerals(array $files)
    {
        // TODO : effectué la logique métier d'insertion des médias pour les articles de type "minéraux"
    }

    public function convertKingdomClassification($kingdomClassification)
    {
        $kingdom = "";
        
        if($kingdomClassification == "Animalia") {
            $kingdom = "animals";
        } elseif($kingdomClassification == "Plantae") {
            $kingdom = "plants";
        } elseif($kingdomClassification == "Fungi") {
            $kingdom = "fungis";
        } elseif($kingdomClassification == "Insecta") {
            $kingdom = "insects";
        } elseif($kingdomClassification == "Bacteria") {
            $kingdom = "bacteria";
        } elseif($kingdomClassification == "Virae") {
            $kingdom = "virus";
        }

        return $kingdom;
    }
}