<?php

namespace App\Repository;

use App\Entity\MediaGallery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MediaGallery|null find($id, $lockMode = null, $lockVersion = null)
 * @method MediaGallery|null findOneBy(array $criteria, array $orderBy = null)
 * @method MediaGallery[]    findAll()
 * @method MediaGallery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediaGalleryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MediaGallery::class);
    }

    public function getMediaGallery($offset, $limit)
    {
        return $this->createQueryBuilder('m')
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getMediaGalleryByID(int $id)
    {
        return $this->createQueryBuilder('m')
            ->where("m.id = :media")
            ->setParameter('media', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getMediaGalleryByType($type, $offset, $limit)
    {
        return $this->createQueryBuilder('m')
            ->where('m.mediaType = :mediaType')
            ->setParameter('mediaType', $type)
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getMediaGalleryByName($filename)
    {
        return $this->createQueryBuilder('m')
            ->where('m.name = :filename')
            ->setParameter('filename', $filename)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Count all media whoes media type (image, video) corresponding to researched type
     * 
     * @param string type of medias
     * @return int number of media corresponding to media type
     */
    public function countMediaGalleryByType(string $type)
    {
        return $this->createQueryBuilder('m')
            ->select("COUNT(m.id) as nbrMedias")
            ->where('m.mediaType = :mediaType')
            ->setParameter('mediaType', $type)
            ->getQuery()
            ->getSingleResult()["nbrMedias"];
    }
}
