<?php

namespace App\Security;

use App\Entity\User;
use App\Entity\UserType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;

/**
 * Checks whether the user meets certain critiera or sends
 * an error page:
 *
 * - students must have exactly one associated student
 * - parents must have at least one associated student
 * - teachers must have one associated teacher
 */
class UserChecker implements EventSubscriberInterface {

    /**
     * @param AuthenticationSuccessEvent $event
     * @throws InvalidAccountException
     */
    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event) {
        $user = $event->getAuthenticationToken()->getUser();

        if(!$user instanceof User) {
            return;
        }

        if($user->getUserType()->equals(UserType::Teacher()) && $user->getTeacher() === null) {
            throw new InvalidAccountException('invalid_account.teacher');
        }

        if($user->getUserType()->equals(UserType::Student()) && $user->getStudents()->count() !== 1) {
            throw new InvalidAccountException('invalid_account.student');
        }

        if($user->getUserType()->equals(UserType::Parent()) && $user->getStudents()->count() === 0) {
            throw new InvalidAccountException('invalid_account.parent');
        }
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array {
        return [
            AuthenticationSuccessEvent::class => 'onAuthenticationSuccess'
        ];
    }
}