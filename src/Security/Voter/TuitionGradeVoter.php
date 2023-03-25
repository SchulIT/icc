<?php

namespace App\Security\Voter;

use App\Entity\TuitionGrade;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\The;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TuitionGradeVoter extends Voter {

    public const New = 'new';
    public const Edit = 'edit';

    protected function supports(string $attribute, mixed $subject): bool {
        return in_array($attribute, [ self::New, self::Edit])
            && $subject instanceof TuitionGrade;
    }

    /**
     * @param string $attribute
     * @param TuitionGrade $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool {
        /** @var User $user */
        $user = $token->getUser();

        if(!$user->isTeacher()) {
            return false;
        }

        if(!$subject->getTuition() === null) {
            return false;
        }

        foreach($subject->getTuition()->getTeachers() as $teacher) {
            if($teacher->getId() === $user->getTeacher()->getId()) {
                return true;
            }
        }

        return false;
    }
}