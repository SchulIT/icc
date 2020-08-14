<?php

namespace App\Security\Voter;

use App\Entity\Student;
use App\Entity\Tuition;
use App\Entity\User;
use App\Entity\UserType;
use App\Utils\EnumArrayUtils;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TuitionVoter extends Voter {

    public const View = 'view';

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject) {
        return $attribute === static::View && $subject instanceof Tuition;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        $blocked = [
            UserType::Student(),
            UserType::Parent()
        ];

        return EnumArrayUtils::inArray($user->getUserType(), $blocked) === false;
    }
}