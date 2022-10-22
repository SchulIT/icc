<?php

namespace App\Security\Voter;

use App\Entity\StudentAbsenceType;
use App\Entity\User;
use App\Entity\UserTypeEntity;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class StudentAbsenceTypeVoter extends Voter {

    public const USE = 'use';

    protected function supports(string $attribute, $subject) {
        return $attribute === self::USE && $subject instanceof StudentAbsenceType;
    }

    /**
     * @param string $attribute
     * @param StudentAbsenceType $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token) {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        /** @var UserTypeEntity $allowedUserType */
        foreach($subject->getAllowedUserTypes() as $allowedUserType) {
            if($allowedUserType->getUserType()->equals($user->getUserType())) {
                return true;
            }
        }

        return false;
    }
}