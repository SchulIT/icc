<?php

namespace App\Security\Voter;

use App\Entity\MessageScope;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class MessageScopeVoter extends Voter {

    public const USE = 'use';

    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker) {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject) {
        return $attribute === static::USE
            && $subject instanceof MessageScope;
    }

    /**
     * @param string $attribute
     * @param MessageScope $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
        if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return MessageScope::Messages()->equals($subject);
    }
}