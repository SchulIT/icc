<?php

namespace App\Chat;

use App\Entity\Chat;
use App\Entity\User;
use App\Repository\ChatMessageRepositoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ChatSeenByHelper {
    public function __construct(private readonly ChatMessageRepositoryInterface $messageRepository, private readonly TokenStorageInterface $tokenStorage) {

    }

    public function markAllChatMessagesSeen(Chat $chat): void {
        $user = $this->tokenStorage->getToken()?->getUser();

        if(!$user instanceof User) {
            return;
        }

        foreach($chat->getMessages() as $message) {
            if(!$message->getSeenBy()->contains($user) && $message->getCreatedBy()->getId() !== $user->getId()) {
                $message->addSeenBy($user);
                $this->messageRepository->persist($message);
            }
        }
    }
}