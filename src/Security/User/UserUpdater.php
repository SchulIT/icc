<?php

namespace App\Security\User;

use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use SchulIT\CommonBundle\Security\AuthenticationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserUpdater implements EventSubscriberInterface {

    private $userMapper;
    private $userRepository;

    public function __construct(UserMapper $userMapper, UserRepositoryInterface $userRepository) {
        $this->userMapper = $userMapper;
        $this->userRepository = $userRepository;
    }

    public function onAuthentication(AuthenticationEvent $event) {
        /** @var User $user */
        $user = $event->getUser();

        $this->userMapper->mapUser($user, $event->getToken()->getResponse());
        $this->userRepository->persist($user);
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents() {
        return [
            AuthenticationEvent::class => 'onAuthentication'
        ];
    }
}