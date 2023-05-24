<?php

namespace App\Security\Voter;

use App\Entity\TeacherAbsence;
use App\Entity\User;
use App\Settings\TeacherAbsenceSettings;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TeacherAbsenceVoter extends Voter {

    public const NewAbsence = 'new-teacher-absence';

    public const CanViewAny = 'view-any-teacher-absence';
    public const Index = 'index';
    public const Edit = 'edit';
    public const Show = 'show';
    public const Remove = 'remove';

    public const Process = 'process';

    public function __construct(private readonly AccessDecisionManagerInterface $accessDecisionManager, private readonly TeacherAbsenceSettings $settings) { }

    protected function supports(string $attribute, mixed $subject): bool {
        return $attribute === self::NewAbsence
            || $attribute === self::CanViewAny
            || $attribute === self::Index
            || ($subject instanceof TeacherAbsence && in_array($attribute, [ self::Edit, self::Show, self::Remove, self::Process ]));
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool {
        if($this->settings->isEnabled() !== true) {
            return false;
        }

        switch($attribute) {
            case self::Index:
                return $this->canCreate($token) || $this->canViewAny($token);

            case self::NewAbsence:
                return $this->canCreate($token);

            case self::CanViewAny:
                return $this->canViewAny($token);

            case self::Edit:
                return $this->canEdit($token, $subject);

            case self::Show:
                return $this->canShow($token, $subject);

            case self::Remove:
                return $this->canRemove($token, $subject);

            case self::Process:
                return $this->canProcess($token, $subject);
        }

        throw new LogicException('This code should not be executed.');
    }

    private function canViewAny(TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, [ 'ROLE_TEACHER_ABSENCE_MANAGER']);
    }

    private function canCreate(TokenInterface $token): bool {
        if($token->getUser() instanceof User && $token->getUser()->isTeacher()) {
            return true;
        }

        return $this->accessDecisionManager->decide($token, ['ROLE_TEACHER_ABSENCE_MANAGER']);
    }

    private function canInteractIfTeacherOrManager(TokenInterface $token, TeacherAbsence $absence): bool {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        if($user->getTeacher() !== null && $absence->getTeacher()->getId() === $user->getTeacher()->getId()) {
            // Teachers can edit/show/remove their own absences
            return true;
        }

        return $this->accessDecisionManager->decide($token, ['ROLE_TEACHER_ABSENCE_MANAGER']);
    }

    private function canEdit(TokenInterface $token, TeacherAbsence $absence): bool {
        return $this->canInteractIfTeacherOrManager($token, $absence);
    }

    private function canShow(TokenInterface $token, TeacherAbsence $absence): bool {
        return $this->canInteractIfTeacherOrManager($token, $absence);
    }

    private function canRemove(TokenInterface $token, TeacherAbsence $absence): bool {
        return $this->canInteractIfTeacherOrManager($token, $absence);
    }

    private function canProcess(TokenInterface $token, TeacherAbsence $absence): bool {
        return $this->accessDecisionManager->decide($token, ['ROLE_TEACHER_ABSENCE_MANAGER']);
    }
}