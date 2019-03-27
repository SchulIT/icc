<?php

namespace App\Message;

use App\Entity\Message;
use App\Entity\MessageConfirmation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MessageConfirmationHelper {

    private $entityManager;
    private $authorizationChecker;

    public function __construct(EntityManagerInterface $entityManager, AuthorizationCheckerInterface $authorizationChecker) {
        $this->entityManager = $entityManager;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function confirm(Message $message, User $user) {
        $confirmedUserIds = array_map(function (MessageConfirmation $confirmation) {
            return $confirmation->getUser()->getId();
        }, $message->getConfirmations()->toArray());

        if(!in_array($user->getId(), $confirmedUserIds)) {
            $confirmation = (new MessageConfirmation())
                ->setUser($user)
                ->setMessage($message);

            $this->entityManager->persist($confirmation);
            $this->entityManager->flush();
        }
    }
}