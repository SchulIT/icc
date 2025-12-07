<?php

namespace App\Security\Voter;

use App\Entity\StudentLearningManagementSystemInformation;
use App\Entity\User;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CredentialsVoter extends Voter {

    public const string View = 'view';
    public const string ViewAny = 'view-credentials';

    protected function supports(string $attribute, mixed $subject): bool {
        if($attribute === self::ViewAny) {
            return true;
        }

        return $attribute == self::View && $subject instanceof StudentLearningManagementSystemInformation;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, Vote|null $vote = null): bool {
        switch($attribute) {
            case self::ViewAny:
                return $this->canViewAny($token);

            case self::View:
                return $this->canView($subject, $token);
        }

        throw new LogicException('This code should not be reached!');
    }

    private function getUser(TokenInterface $token): ?User {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return null;
        }

        return $user;
    }

    private function canViewAny(TokenInterface $token): bool {
        $user = $this->getUser($token);

        if($user === null) {
            return false;
        }

        return $user->isStudent();
    }

    private function canView(StudentLearningManagementSystemInformation $info, TokenInterface $token): bool {
        $user = $this->getUser($token);

        if($user === null) {
            return false;
        }

        if(!$user->isStudent()) {
            return false;
        }

        foreach($user->getStudents() as $student) {
            if($student->getId() === $info->getStudent()?->getId()) {
                return true;
            }
        }

        return false;
    }
}