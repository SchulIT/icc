<?php

namespace App\Message;

use App\Entity\Message;
use App\Entity\User;
use App\Repository\UserRepositoryInterface;

class DismissedMessagesHelper {

    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository) {
        $this->userRepository = $userRepository;
    }

    /**
     * @param Message $message
     * @param User $user
     */
    public function dismiss(Message $message, User $user) {
        if($user->getDismissedMessages()->contains($message) === false) {
            $user->addDismissedMessage($message);
            $this->userRepository->persist($user);
        }
    }

    public function reenable(Message $message, User $user) {
        if($user->getDismissedMessages()->contains($message) === true) {
            $user->removeDismissedMessage($message);
            $this->userRepository->persist($user);
        }
    }

    /**
     * @param Message[] $messages
     * @param User $user
     * @return Message[]
     */
    public function getDismissedMessages(array $messages, User $user) {
        $dismissedIds = array_map(function(Message $message) {
            return $message->getId();
        }, $user->getDismissedMessages()->toArray());

        $dismissedMessages = [ ];

        foreach($messages as $message) {
            if(in_array($message->getId(), $dismissedIds)) {
                $dismissedMessages[] = $message;
            }
        }

        return $dismissedMessages;
    }

    /**
     * @param Message[] $messages
     * @param User $user
     * @return Message[]
     */
    public function getNonDismissedMessages(array $messages, User $user) {
        $dismissedIds = array_map(function(Message $message) {
            return $message->getId();
        }, $user->getDismissedMessages()->toArray());

        $dismissedMessages = [ ];

        foreach($messages as $message) {
            if(!in_array($message->getId(), $dismissedIds)) {
                $dismissedMessages[] = $message;
            }
        }

        return $dismissedMessages;
    }
}