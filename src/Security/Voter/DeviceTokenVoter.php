<?php

namespace App\Security\Voter;

use App\Entity\IcsAccessToken;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class DeviceTokenVoter extends Voter {

    public const Remove = 'remove';

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject) {
        return $attribute === static::Remove && $subject instanceof IcsAccessToken;
    }

    /**
     * @param string $attribute
     * @param IcsAccessToken $subject
     * @param TokenInterface $token
     * @return bool|void
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
        /** @var User|null $user */
        $user = $token->getUser();

        if($user === null) {
            return false;
        }

        return $subject->getUser()->getId() === $user->getId();
    }
}