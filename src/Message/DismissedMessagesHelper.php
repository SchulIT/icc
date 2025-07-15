<?php

namespace App\Message;

use App\Entity\Message;
use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

readonly class DismissedMessagesHelper {

    public function __construct(private TokenStorageInterface $tokenStorage, private UserRepositoryInterface $userRepository)
    {
    }

    public function dismiss(Message $message, User $user): void {
        if($user->getDismissedMessages()->contains($message) === false) {
            $user->addDismissedMessage($message);
            $this->userRepository->persist($user);
        }
    }

    public function reenable(Message $message, User $user): void {
        if($user->getDismissedMessages()->contains($message) === true) {
            $user->removeDismissedMessage($message);
            $this->userRepository->persist($user);
        }
    }

    /**
     * @param Message[] $messages
     * @return Message[]
     */
    public function getDismissedMessages(array $messages, User $user): array {
        $dismissedIds = array_map(fn(Message $message) => $message->getId(), $user->getDismissedMessages()->toArray());

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
     * @return Message[]
     */
    public function getNonDismissedMessages(array $messages, User $user): array {
        $dismissedIds = array_map(fn(Message $message) => $message->getId(), $user->getDismissedMessages()->toArray());

        $dismissedMessages = [ ];

        foreach($messages as $message) {
            if(!in_array($message->getId(), $dismissedIds)) {
                $dismissedMessages[] = $message;
            }
        }

        return $dismissedMessages;
    }

    public function isMessageDismissed(Message $message, User|null $user = null): bool {
        if($user === null) {
            /** @var User $user */
            $user = $this->tokenStorage->getToken()->getUser();
        }

        /** @var Message[] $dismissedMessages */
        $dismissedMessages = $user->getDismissedMessages();

        foreach($dismissedMessages as $dismissedMessage) {
            if($dismissedMessage->getId() === $message->getId()) {
                return true;
            }
        }

        return false;
    }
}