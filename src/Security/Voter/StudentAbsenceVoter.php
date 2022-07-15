<?php

namespace App\Security\Voter;

use App\Entity\GradeTeacher;
use App\Entity\StudentAbsence;
use App\Entity\Student;
use App\Entity\User;
use App\Entity\UserType;
use App\Repository\SectionRepositoryInterface;
use App\Section\SectionResolverInterface;
use LogicException;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class StudentAbsenceVoter extends Voter {

    public const New = 'new-absence';
    public const View = 'view';
    public const CanViewAny = 'view-any-absence';

    public const Approve = 'approve';
    public const Deny = 'deny';

    private DateHelper $dateHelper;
    private SectionResolverInterface $sectionResolver;
    private AccessDecisionManagerInterface $accessDecisionManager;

    public function __construct(DateHelper $dateHelper, SectionResolverInterface $sectionResolver, AccessDecisionManagerInterface $accessDecisionManager) {
        $this->dateHelper = $dateHelper;
        $this->sectionResolver = $sectionResolver;
        $this->accessDecisionManager = $accessDecisionManager;
    }

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject): bool {
        return $attribute === self::New
            || $attribute === self::CanViewAny
            || (in_array($attribute, [ self::View, self::Approve, self::Deny ]) && $subject instanceof StudentAbsence);
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool {
        switch($attribute) {
            case self::New:
                return $this->canCreate($token);

            case self::View:
                return $this->canView($token, $subject);

            case self::CanViewAny:
                return $this->canViewAny($token);

            case self::Approve:
            case self::Deny:
                return $this->canApproveOrDeny($token, $subject);
        }

        throw new LogicException('This code should not be reached.');
    }

    private function canCreate(TokenInterface $token): bool {
        if($this->accessDecisionManager->decide($token, ['ROLE_STUDENT_ABSENCE_CREATOR']) === true || $this->accessDecisionManager->decide($token, ['ROLE_ADMIN']) ) {
            return true;
        }

        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        $isStudent = $user->getUserType()->equals(UserType::Student());
        $isParent = $user->getUserType()->equals(UserType::Parent());

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

    private function canApproveOrDeny(TokenInterface $token, StudentAbsence $absence): bool {
        if($this->accessDecisionManager->decide($token, ['ROLE_STUDENT_ABSENCE_APPROVER'])) {
            return true;
        }

        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        if(!UserType::Teacher()->equals($user->getUserType()) || $user->getTeacher() === null) {
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