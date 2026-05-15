<?php

namespace App\Common\Voter;

use App\Common\Entity\Tuition;
use App\Common\Entity\User;
use App\Common\Entity\UserType;
use App\Framework\Utils\ArrayUtils;
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