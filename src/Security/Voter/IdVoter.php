<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class IdVoter extends Voter {

    public const Id = 'show-id';

    public function __construct(private readonly AccessDecisionManagerInterface $accessDecisionManager) {

    }

    protected function supports(string $attribute, mixed $subject): bool {
        return $attribute === self::Id;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, Vote|null $vote = null): bool {
        return $this->accessDecisionManager->decide($token, ['ROLE_ADMIN']);
    }
}