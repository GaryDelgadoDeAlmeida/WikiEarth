<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\MediaGalleryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin", name="admin")
 */
class MediaGalleryController extends AbstractController
{
    private User $user;
    private MediaGalleryRepository $mediaGalleryRepository;

    function __construct(
        Security $security,
        MediaGalleryRepository $mediaGalleryRepository
    ) {
        $this->user = $security->getUser();
        $this->mediaGalleryRepository = $mediaGalleryRepository;
    }

    /**
     * @Route("/media", name="Media")
     */
    public function admin_media(Request $request) : Response
    {
        return $this->render('admin/media/index.html.twig');
    }

    /**
     * @Route("/media/{type}", name="MediaType")
     */
    public function admin_media_by_type(Request $request, string $type) : Response
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $medias = $this->mediaGalleryRepository->getMediaGalleryByType($type, $offset, $limit);
        $nbrOffset = ceil($this->mediaGalleryRepository->countMediaGalleryByType($type) / $limit);

        return $this->render('admin/media/list.html.twig', [
            "mediaType" => $type,
            "medias" => $medias,
            "offset" => $offset,
            "nbrOffset" => $nbrOffset,
        ]);
    }

    /**
     * @Route("/media/{id}/delete", name="DeleteMediaByID", methods="DELETE")
     */
    public function admin_delete_media_by_id(int $id)
    {
        $media = $this->mediaGalleryRepository->getMediaGalleryByID($id);
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

        $this->mediaGalleryRepository->remove($media, true);
        
        return $this->json([
            "error" => false,
            "class" => "success",
            "message" => "The media has been successfully deleted"
        ]);
    }
}
