<?php

namespace App\Security;

use App\Entity\IcsAccessToken;
use App\Entity\User;
use App\Entity\UserType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Checks whether the user meets certain critiera or sends
 * an error page:
 *
 * - students must have exactly one associated student
 * - parents must have at least one associated student
 * - teachers must have one associated teacher
 */
class UserChecker implements UserCheckerInterface {

    public function checkPreAuth(UserInterface $user) {
        
    }

    public function checkPostAuth(UserInterface $user) {
        if($user instanceof IcsAccessToken) {
            $user = $user->getUser();
        }

        if(!$user instanceof User) {
            return;
        }

        if($user->isTeacher() && $user->getTeacher() === null) {
            throw new InvalidAccountException('invalid_account.teacher');
        }

        if($user->isStudent() && $user->getStudents()->count() !== 1) {
            throw new InvalidAccountException('invalid_account.student');
        }

        if($user->isParent() && $user->getStudents()->count() === 0) {
            throw new InvalidAccountException('invalid_account.parent');
        }
    }
}