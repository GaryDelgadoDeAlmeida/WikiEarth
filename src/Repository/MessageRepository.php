<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * @param int user id
     * @param int chat room id
     * @return Message[] Returns an array of Message object
     */
    public function getMessagesOfUserByChatRoom(int $user_id, int $chat_room_id)
    {
        return $this->createQueryBuilder("m")
            ->where("m.user = :user_id")
            ->andWhere("m.chat_room = :chat_room_id")
            ->setParameters([
                "user_id" => $user_id,
                "chat_room_id" => $chat_room_id,
            ])
            ->getQuery()
            ->getResult()
        ;
    }
}
