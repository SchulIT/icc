<?php

namespace App\Security\User;

use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use LightSaml\SpBundle\Security\Http\Authenticator\SamlToken;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;

readonly class UserUpdater implements EventSubscriberInterface {

    public function __construct(private UserMapper $userMapper, private UserRepositoryInterface $userRepository)
    {
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void {
        $token = $event->getAuthenticationToken();
        $user = $token->getUser();

        if(!$user instanceof User || !$token instanceof SamlToken) {
            return;
        }

        $this->userMapper->mapUser($user, $token->getAttributes());
        $this->userRepository->persist($user);
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array {
        return [
            AuthenticationSuccessEvent::class => ['onAuthenticationSuccess', 512], // must be higher than the priority of the UserCheckerListener
        ];
    }
}