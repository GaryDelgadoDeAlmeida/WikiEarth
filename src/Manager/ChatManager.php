<?php

namespace App\Manager;

use App\Entity\{User, ChatRoom};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class ChatManager {

    private $em;

    function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Request request
     * @return array
     */
    public function prepareSideBar(Request $request, User $current_logged_user)
    {
        $userCandidate = $currentDiscussion = [];
        $startedDiscussions = $this->em->getRepository(ChatRoom::class)->getAllDiscussionsOfUser($current_logged_user->getId());

        $user_id = $request->get("user");
        $user_id = !empty($user_id) && \preg_match("/^[0-9]*$/", $user_id) ? (int)$user_id : null;
        if(!empty($user_id)) {
            $userCandidate = $this->em->getRepository(User::class)->getUser($user_id);
            $oneDiscussion = $this->em->getRepository(ChatRoom::class)->getDiscussionOfUserAndParticipant($current_logged_user->getId(), $user_id);
            
            // We check if there is an existing discussion between the current user and the participant
            if(!empty($oneDiscussion)) {
                $currentDiscussion = $oneDiscussion;
            }
        }

        return [
            "user" => $current_logged_user,
            "startedDiscussions" => $startedDiscussions,
            "userCandidate" => $userCandidate,
            "currentDiscussion" => $currentDiscussion
        ];
    }
}