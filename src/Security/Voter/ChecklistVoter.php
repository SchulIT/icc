<?php

namespace App\Security\Voter;

use App\Entity\Checklist;
use App\Entity\ChecklistStudent;
use App\Entity\User;
use App\Form\ChecklistStudentType;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ChecklistVoter extends Voter {

    public const string Add = 'new-checklist';
    public const string Edit = 'edit';
    public const string Remove = 'remove';
    public const string View = 'view';

    protected function supports(string $attribute, mixed $subject): bool {
        if($attribute === self::Add) {
            return true;
        }

        return in_array($attribute, [self::Edit, self::Remove, self::View, self::Remove])
            && $subject instanceof Checklist;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, Vote|null $vote = null): bool {
        switch($attribute) {
            case self::Add:
                return $this->canAdd($token);
            case self::Edit:
                return $this->canEdit($subject, $token);
            case self::Remove:
                return $this->canRemove($subject, $token);
            case self::View:
                return $this->canView($subject, $token);
        }

        throw new LogicException('This code should not be reached.');
    }

    private function canAdd(TokenInterface $token): bool {
        $user = $token->getUser();
        return $user instanceof User && $user->isTeacher();
    }

    private function canView(Checklist $checklist, TokenInterface $token): bool {
        if($this->canEdit($checklist, $token)) {
            return true;
        }

        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        if($user->isStudentOrParent()) {
            /** @var ChecklistStudent $student */
            foreach($checklist->getStudents() as $student) {
                if($user->getStudents()->contains($student->getStudent())) {
                    return ($user->isStudent() && $checklist->isCanStudentsView())
                        || ($user->isParent() && $checklist->isCanParentsView());
                }
            }
        }

        return $checklist->getSharedWith()->contains($user);
    }

    public function canEdit(Checklist $checklist, TokenInterface $token): bool {
        $user = $token->getUser();

        return $user instanceof User
            && ($checklist->getCreatedBy()->getId() === $user->getId() || $checklist->getSharedWith()->contains($user));
    }

    public function canRemove(Checklist $checklist, TokenInterface $token): bool {
        $user = $token->getUser();

        return $user instanceof User && $checklist->getCreatedBy()->getId() === $user->getId();
    }
}