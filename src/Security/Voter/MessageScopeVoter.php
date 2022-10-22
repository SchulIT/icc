<?php

namespace App\Security\Voter;

use App\Entity\MessageScope;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class MessageScopeVoter extends Voter {

    public const USE = 'use';

    public function __construct(private AuthorizationCheckerInterface $authorizationChecker)
    {
    }

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject): bool {
        return $attribute === self::USE
            && $subject instanceof MessageScope;
    }

    /**
     * @param MessageScope $subject
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool {
        if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return MessageScope::Messages()->equals($subject);
    }
}