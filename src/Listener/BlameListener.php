<?php

namespace App\Listener;

use App\Security\CurrentUserResolver;
use Gedmo\Blameable\BlameableListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Sets the username from the security context by listening on kernel.request
 *
 * @author David Buchmann <mail@davidbu.ch>
 * @author Marcel Marnitz <marcelmarnitz@outlook.com>
 */
class BlameListener implements EventSubscriberInterface
{
    private $authorizationChecker;
    private $userResolver;
    private $blameableListener;

    public function __construct(BlameableListener $blameableListener, CurrentUserResolver $userResolver, AuthorizationCheckerInterface $authorizationChecker = null)
    {
        $this->blameableListener = $blameableListener;
        $this->userResolver = $userResolver;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @internal
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $user = $this->userResolver->getUser();
        if (null !== $user && $this->authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $this->blameableListener->setUserValue($user);
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => ['onKernelRequest', -1 ],
        );
    }
}
