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
    protected function supports($attribute, $subject): bool {
        return $attribute === self::Remove && $subject instanceof IcsAccessToken;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool {
        /** @var User|null $user */
        $user = $token->getUser();

        if($user === null) {
            return false;
        }

        return $subject->getUser()->getId() === $user->getId();
    }
}