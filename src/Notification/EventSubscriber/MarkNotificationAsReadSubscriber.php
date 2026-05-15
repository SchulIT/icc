<?php

namespace App\Notification\EventSubscriber;

use App\Common\Entity\User;
use App\Notification\Repository\NotificationRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * This subscriber marks notification "as read" as soon as the page is requested which the notification contains as link
 */
class MarkNotificationAsReadSubscriber implements EventSubscriberInterface {

    public function __construct(private readonly TokenStorageInterface $tokenStorage, private readonly NotificationRepositoryInterface $notificationRepository) {

    }

    public function onKernelRequest(RequestEvent $event): void {
        $user = $this->tokenStorage->getToken()?->getUser();

        if(!$user instanceof User) {
            return;
        }

        $uri = $event->getRequest()->getUri();

        $this->notificationRepository->markAllReadForUserAndLink($user, $uri);
    }

    public static function getSubscribedEvents(): array {
        return [
            KernelEvents::REQUEST => 'onKernelRequest'
        ];
    }

}