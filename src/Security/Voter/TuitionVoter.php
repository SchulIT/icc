<?php

namespace App\Security\Voter;

use App\Entity\Tuition;
use App\Entity\User;
use App\Entity\UserType;
use App\Utils\ArrayUtils;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TuitionVoter extends Voter {

    public const View = 'view';

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject): bool {
        return $attribute === self::View && $subject instanceof Tuition;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token, Vote|null $vote = null): bool {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        $blocked = [
            UserType::Student,
            UserType::Parent
        ];

        return ArrayUtils::inArray($user->getUserType(), $blocked) === false;
    }
}