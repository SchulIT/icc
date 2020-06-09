<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Trikoder\Bundle\OAuth2Bundle\Model\AccessToken;

class AccessTokenVoter extends Voter {

    public const Revoke = 'revoke';

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject) {
        return $attribute === static::Revoke && $subject instanceof AccessToken;
    }

    /**
     * @param string $attribute
     * @param AccessToken $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
        return $subject->getUserIdentifier() === $token->getUsername();
    }
}