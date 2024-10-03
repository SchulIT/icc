<?php

namespace App\Security\Firewall;

use App\Security\Firewall\Attribute\IsGrantedIfNotImpersonated;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\SwitchUserToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DisableChatWhenImpersonatedSubscriber implements EventSubscriberInterface {

    public function __construct(private readonly TokenStorageInterface $tokenStorage) {

    }

    public function onKernelControllerArguments(ControllerArgumentsEvent $event): void {
        $isGrantedIfNotImpersonatedAttribute = $event->getAttributes(IsGrantedIfNotImpersonated::class);

        if(empty($isGrantedIfNotImpersonatedAttribute)) {
            // Attribute not present
            return;
        }

        // Attribute present
        $token = $this->tokenStorage->getToken();

        if($token instanceof SwitchUserToken) {
            throw new AccessDeniedException('User impersonation is active. This controller is disabled.');
        }
    }

    public static function getSubscribedEvents(): array {
        return [
            KernelEvents::CONTROLLER_ARGUMENTS => 'onKernelControllerArguments'
        ];
    }
}