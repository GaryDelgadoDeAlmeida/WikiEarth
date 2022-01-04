<?php

namespace App\Repository;

use App\Entity\ChatRoom;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ChatRoom|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChatRoom|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChatRoom[]    findAll()
 * @method ChatRoom[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChatRoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChatRoom::class);
    }

    /**
     * @param int chat room id
     * @return ChatRoom
     */
    public function getDiscussion(int $chatRoom)
    {
        return $this->createQueryBuilder("c")
            ->where("c.id = :chatRoom")
            ->setParameter("chatRoom", $chatRoom)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @param int user id
     * @return ChatRoom[] Returns an array of ChatRoom objects
     */
    public function getAllDiscussionsOfUser(int $user_id)
    {
        return $this->createQueryBuilder("c")
            ->where("c.user = :user_id")
            ->orWhere("c.participant = :user_id")
            ->setParameter('user_id', $user_id)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param int user id
     * @param int participant id / the second user id
     * @return ChatRoom Returns a object of ChatRoom
     */
    public function getDiscussionOfUserAndParticipant(int $user_id, int $participant_id)
    {
        return $this->createQueryBuilder("c")
            ->where("c.user = :user_id AND c.participant = :participant_id")
            ->orWhere("c.user = :participant_id AND c.participant = :user_id")
            ->setParameters([
                "user_id" => $user_id,
                "participant_id" => $participant_id
            ])
            ->getQuery()
            ->getOneOrNullResult();
        ;
    }
}
