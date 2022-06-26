<?php

namespace App\Security\User;

use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use LightSaml\SpBundle\Security\Http\Authenticator\SamlToken;
use SchulIT\CommonBundle\Security\AuthenticationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class UserUpdater implements EventSubscriberInterface {

    private UserMapper $userMapper;
    private UserRepositoryInterface $userRepository;

    public function __construct(UserMapper $userMapper, UserRepositoryInterface $userRepository) {
        $this->userMapper = $userMapper;
        $this->userRepository = $userRepository;
    }

    public function onLoginSuccess(LoginSuccessEvent $event) {
        $user = $event->getUser();
        $token = $event->getAuthenticatedToken();

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
            LoginSuccessEvent::class => 'onLoginSuccess'
        ];
    }
}