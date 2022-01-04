<?php

namespace App\Manager;

use App\Entity\{User, Message, ChatRoom};
use Doctrine\ORM\EntityManagerInterface;

class ChatMessageManager {

    private $em;

    function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param ChatRoom chat room
     * @param User user to attach the message
     * @param string message of the user
     * @return Message Returns message object
     */
    public function insertMessage(ChatRoom $chatRoom, User $user, string $candidateMessage)
    {
        $message = new Message();
        $message->setChatRoom($chatRoom);
        $message->setSender($user);
        $message->setContent($candidateMessage);
        $message->setIsRead(false);
        $message->setCreatedAt(new \DateTimeImmutable());

        $this->em->persist($message);
        $this->em->flush();
        $this->em->clear();

        return $message;
    }
}