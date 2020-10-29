<?php

namespace App\Security\Voter;

use App\Entity\Student;
use App\Entity\User;
use App\Entity\UserType;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class SickNoteVoter extends Voter {

    public const New = 'new-sicknote';

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject) {
        return static::New;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        $isStudent = $user->getUserType()->equals(UserType::Student());
        $isParent = $user->getUserType()->equals(UserType::Parent());

        if($isParent === true) {
            return true;
        }

        if($isStudent === false) {
            return false;
        }

        /** @var Student $student */
        foreach($user->getStudents() as $student) {
            if ($student->isFullAged() === true) {
                return true;
            }
        }

        return false;
    }
}