<?php

namespace App\Security;

use App\Entity\User;
use App\Entity\UserType;
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

    /**
     * @inheritDoc
     */
    public function checkPreAuth(UserInterface $user) {
        return;
    }

    /**
     * @inheritDoc
     */
    public function checkPostAuth(UserInterface $user) {
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
}