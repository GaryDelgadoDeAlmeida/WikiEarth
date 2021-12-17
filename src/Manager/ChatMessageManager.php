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

    public function insertMessage(User $user, Message $message)
    {
        # code...
    }
}