<?php

namespace App\Security\Voter;

use App\Entity\GradeTeacher;
use App\Entity\StudentAbsence;
use App\Entity\Student;
use App\Entity\User;
use App\Entity\UserType;
use App\Repository\SectionRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Settings\StudentAbsenceSettings;
use LogicException;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class StudentAbsenceVoter extends Voter {

    public const New = 'new-absence';
    public const View = 'view';
    public const CanViewAny = 'view-any-absence';
    public const Bulk = 'new-absence-bulk';

    public const Approve = 'approve';
    public const Deny = 'deny';
    public const Edit = 'edit';

    public const Remove = 'remove';

    public function __construct(private readonly DateHelper $dateHelper, private readonly SectionResolverInterface $sectionResolver, private readonly AccessDecisionManagerInterface $accessDecisionManager, private readonly StudentAbsenceSettings $settings)
    {
    }

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject): bool {
        return $attribute === self::New
            || $attribute === self::CanViewAny
            || $attribute === self::Bulk
            || (in_array($attribute, [ self::Edit, self::View, self::Approve, self::Deny, self::Remove ]) && $subject instanceof StudentAbsence);
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        if($this->settings->isEnabled() !== true) {
            return false;
        }

        return match ($attribute) {
            self::New => $this->canCreate($token),
            self::Bulk => $this->canCreateBulk($token),
            self::View => $this->canView($token, $subject),
            self::Edit => $this->canEdit($token, $subject),
            self::Remove => $this->canRemove($token, $subject),
            self::CanViewAny => $this->canViewAny($token),
            self::Approve, self::Deny => $this->canApproveOrDeny($token, $subject),
            default => throw new LogicException('This code should not be reached.'),
        };
    }
    private function canCreate(TokenInterface $token): bool {
        if($this->accessDecisionManager->decide($token, ['ROLE_STUDENT_ABSENCE_CREATOR']) === true || $this->accessDecisionManager->decide($token, ['ROLE_ADMIN']) ) {
            return true;
        }

        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        $isStudent = $user->isStudent();
        $isParent = $user->isParent();

        if($isParent === true) {
            return true;
        }

        if($isStudent === false) {
            return false;
        }

        /** @var Student $student */
        foreach($user->getStudents() as $student) {
            if ($student->isFullAged($this->dateHelper->getToday()) === true) {
                return true;
            }
        }

        return false;
    }

    private function canCreateBulk(TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, ['ROLE_STUDENT_ABSENCE_CREATOR']) === true || $this->accessDecisionManager->decide($token, ['ROLE_ADMIN']);
    }

    private function canViewAny(TokenInterface $token): bool {
        return $this->canCreate($token) || $this->accessDecisionManager->decide($token, ['ROLE_STUDENT_ABSENCE_VIEWER']) === true;
    }

    private function canView(TokenInterface $token, StudentAbsence $absence): bool {
        if($this->accessDecisionManager->decide($token, ['ROLE_STUDENT_ABSENCE_VIEWER'])) {
            return true;
        }

        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        foreach($user->getStudents() as $student) {
            if($absence->getStudent() === $student) {
                return true;
            }
        }

        return false;
    }

    private function canEdit(TokenInterface $token, StudentAbsence $absence): bool {
        if($this->accessDecisionManager->decide($token, ['ROLE_STUDENT_ABSENCE_VIEWER'])) {
            return true;
        }

        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        return $absence->getCreatedBy()->getId() === $user->getId();
    }

    private function canRemove(TokenInterface $token, StudentAbsence $absence): bool {
        return $this->accessDecisionManager->decide($token, ['ROLE_STUDENT_ABSENCE_VIEWER']);
    }

    private function canApproveOrDeny(TokenInterface $token, StudentAbsence $absence): bool {
        if($this->accessDecisionManager->decide($token, ['ROLE_STUDENT_ABSENCE_APPROVER'])) {
            return true;
        }

        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        if($user->isTeacher() === false || $user->getTeacher() === null) {
            return false;
        }

        $currentSection = $this->sectionResolver->getSectionForDate($absence->getFrom()->getDate());

        if($currentSection === null) {
            return false;
        }

        $grade = $absence->getStudent()->getGrade($currentSection);

        if($grade === null) {
            return false;
        }

        /*
         * Only grade teachers can approve or deny absences
         */

        /** @var GradeTeacher $teacher */
        foreach($grade->getTeachers() as $teacher) {
            if($teacher->getSection()->getId() === $currentSection->getId()
                && $teacher->getId() === $user->getTeacher()->getId()) {
                return true;
            }
        }

        return false;
    }
}