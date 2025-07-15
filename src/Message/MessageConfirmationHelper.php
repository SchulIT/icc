<?php

namespace App\Message;

use App\Entity\Message;
use App\Entity\MessageConfirmation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MessageConfirmationHelper {

    private array $cache = [ ];

    public function __construct(private readonly TokenStorageInterface $tokenStorage, private readonly EntityManagerInterface $entityManager)
    {
    }

    public function isMessageConfirmed(Message $message, User|null $user = null): bool {
        if($user === null) {
            /** @var User $user */
            $user = $this->tokenStorage->getToken()->getUser();
        }

        $this->buildCache($user);
        $confirmedMessageIds = $this->cache[$user->getId()];

        return in_array($message->getId(), $confirmedMessageIds);
    }

    private function buildCache(User $user): void {
        $key = $user->getId();

        if(isset($this->cache[$key])) {
            return;
        }

        /** @var MessageConfirmation[] $confirmations */
        $confirmations = $this->entityManager->createQueryBuilder()
            ->select(['c', 'm'])
            ->from(MessageConfirmation::class, 'c')
            ->leftJoin('c.message', 'm')
            ->leftJoin('c.user', 'u')
            ->where('u.id = :user')
            ->setParameter('user', $user->getId())
            ->getQuery()
            ->getResult();


        $this->cache[$key] = [ ];

        foreach($confirmations as $confirmation) {
            $this->cache[$key][] = $confirmation->getMessage()->getId();
        }
    }
}