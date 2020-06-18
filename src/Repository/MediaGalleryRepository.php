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

    // /**
    //  * @return MediaGallery[] Returns an array of MediaGallery objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MediaGallery
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}