<?php

namespace App\Common\Voter;

use App\Common\Entity\IcsAccessToken;
use App\Common\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class DeviceTokenVoter extends Voter {

    public const Remove = 'remove';

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject): bool {
        return $attribute === self::Remove && $subject instanceof IcsAccessToken;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token, Vote|null $vote = null): bool {
        /** @var User|null $user */
        $user = $token->getUser();

        if($user === null) {
            return false;
        }

        return $subject->getUser()->getId() === $user->getId();
    }
}