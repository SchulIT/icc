<?php

namespace App\Security\Voter;

use App\Entity\ChecklistStudent;
use App\Entity\User;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ChecklistStudentVoter extends Voter {

    public const string View = 'view';
    public const string Edit = 'edit';

    public function __construct(private readonly AccessDecisionManagerInterface $accessDecisionManager) {

    }

    protected function supports(string $attribute, mixed $subject): bool {
        return in_array($attribute, [self::View, self::Edit])
            && $subject instanceof ChecklistStudent;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool {
        switch($attribute) {
            case self::View:
                return $this->canView($subject, $token);

            case self::Edit:
                return $this->canEdit($subject, $token);
        }

        throw new LogicException('This code should not be reached.');
    }

    private function canView(ChecklistStudent $subject, TokenInterface $token): bool {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        if($user->isStudentOrParent() === true && $user->getStudents()->contains($subject->getStudent()) !== true) {
            return false;
        }

        return $this->accessDecisionManager->decide($token, [ChecklistVoter::View], $subject->getChecklist());
    }

    private function canEdit(ChecklistStudent $subject, TokenInterface $token): bool {
        if($this->canView($subject, $token) !== true) {
            return false;
        }

        return $this->accessDecisionManager->decide($token, [ChecklistVoter::Edit], $subject->getChecklist());
    }
}