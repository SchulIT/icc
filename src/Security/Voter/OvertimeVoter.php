<?php

namespace App\Security\Voter;

use App\Entity\User;
use Override;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class OvertimeVoter extends Voter {

    public const string View = 'view-overtime';

    #[Override]
    protected function supports(string $attribute, mixed $subject): bool {
        return $attribute === self::View;
    }

    #[Override]
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        if($user->isTeacher()) {
            return true;
        }

        return false;
    }
}