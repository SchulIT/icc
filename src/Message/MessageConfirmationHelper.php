<?php

namespace App\Message;

use App\Entity\Message;
use App\Entity\MessageConfirmation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MessageConfirmationHelper {

    private $tokenStorage;
    private $entityManager;
    private $cache = [ ];

    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager) {
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
    }

    public function isMessageConfirmed(Message $message, User $user = null) {
        if($user === null) {
            $user = $this->tokenStorage->getToken()->getUser();
        }

        $this->buildCache($user);
        $confirmedMessageIds = $this->cache[$user->getId()];

        return in_array($message->getId(), $confirmedMessageIds);
    }

    private function buildCache(User $user) {
        $key = $user->getId();

        if(isset($this->cache[$key])) {
            return;
        }

        /** @var MessageConfirmation[] $confirmations */
        $confirmations = $this->entityManager->createQueryBuilder()
            ->select(['c', 'm'])
            ->from(MessageConfirmation::class, 'c')
            ->leftJoin('c.message', 'm')
            ->getQuery()
            ->getResult();


        $this->cache[$key] = [ ];

        foreach($confirmations as $confirmation) {
            $this->cache[$key][] = $confirmation->getMessage()->getId();
        }
    }
}