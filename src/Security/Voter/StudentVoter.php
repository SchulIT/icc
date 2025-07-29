<?php

namespace App\Security\Voter;

use App\Entity\Student;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class StudentVoter extends Voter {

    public const Show = 'show';
    public const ShowAny = 'student-details';

    public function __construct(private readonly AccessDecisionManagerInterface $accessDecisionManager) {

    }

    protected function supports(string $attribute, mixed $subject): bool {
        if($attribute === self::ShowAny) {
            return true;
        }

        return $attribute === self::Show && $subject instanceof Student;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, Vote|null $vote = null): bool {
        return $this->accessDecisionManager->decide($token, ['ROLE_STUDENT_VIEWER']);
    }
}