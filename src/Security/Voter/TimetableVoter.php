<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TimetableVoter extends Voter {

    public const Supervisions = 'view_supervisions';

    protected function supports(string $attribute, mixed $subject): bool {
        return $attribute === self::Supervisions;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, Vote|null $vote = null): bool {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        if($user->isStudentOrParent()) {
            return false;
        }

        return true;
    }
}