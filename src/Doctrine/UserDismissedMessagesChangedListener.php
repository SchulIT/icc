<?php

namespace App\Doctrine;

use App\Entity\User;
use App\Repository\NotificationRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: User::class)]
class UserDismissedMessagesChangedListener {

    public function __construct(private readonly NotificationRepositoryInterface $notificationRepository, private readonly UrlGeneratorInterface $urlGenerator) {

    }

    public function postUpdate(User $user, PostUpdateEventArgs $event): void {
        foreach($user->getDismissedMessages() as $message) {
            $uri = $this->urlGenerator->generate('show_message', [
                'uuid' => $message->getUuid()
            ], UrlGeneratorInterface::ABSOLUTE_URL);

            $this->notificationRepository->markAllReadForUserAndLink($user, $uri);
        }
    }
}