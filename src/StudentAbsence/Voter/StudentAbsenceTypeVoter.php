<?php

namespace App\StudentAbsence\Voter;

use App\StudentAbsence\Entity\StudentAbsenceType;
use App\Common\Entity\User;
use App\Common\Entity\UserTypeEntity;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class StudentAbsenceTypeVoter extends Voter {

    public const USE = 'use';

    protected function supports(string $attribute, $subject): bool {
        return $attribute === self::USE && $subject instanceof StudentAbsenceType;
    }

    /**
     * @param StudentAbsenceType $subject
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token, Vote|null $vote = null): bool {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        /** @var UserTypeEntity $allowedUserType */
        foreach($subject->getAllowedUserTypes() as $allowedUserType) {
            if($allowedUserType->getUserType() === $user->getUserType()) {
                return true;
            }
        }

        return false;
    }
}