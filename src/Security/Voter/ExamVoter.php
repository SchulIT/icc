<?php

namespace App\Security\Voter;

use App\Entity\Exam;
use App\Entity\Student;
use App\Entity\Tuition;
use App\Entity\User;
use App\Entity\UserType;
use App\Section\SectionResolverInterface;
use App\Settings\ExamSettings;
use LogicException;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ExamVoter extends Voter {

    public const Show = 'show';
    public const Supervisions = 'supervisions';
    public const Details = 'details';

    public const Manage = 'manage-exams';
    public const Add = 'new-exam';
    public const Edit = 'edit';
    public const Unplan = 'unplan';
    public const Remove = 'remove';

    private DateHelper $dateHelper;
    private ExamSettings $examSettings;
    private AccessDecisionManagerInterface $accessDecisionManager;
    private SectionResolverInterface $sectionResoler;

    public function __construct(DateHelper $dateHelper, ExamSettings $examSettings,
                                AccessDecisionManagerInterface $accessDecisionManager, SectionResolverInterface $sectionResolver) {
        $this->dateHelper = $dateHelper;
        $this->examSettings = $examSettings;
        $this->accessDecisionManager = $accessDecisionManager;
        $this->sectionResoler = $sectionResolver;
    }

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject): bool {
        $attributes = [
            self::Details,
            self::Supervisions,
            self::Show,
            self::Edit,
            self::Remove,
            self::Unplan
        ];

        return in_array($attribute , [ self::Add, self::Manage ]) || ($subject instanceof Exam && in_array($attribute, $attributes));
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool {
        switch($attribute) {
            case self::Show:
                return $this->canViewExam($subject, $token);

            case self::Details:
                return $this->canViewDetails($subject, $token);

            case self::Supervisions:
                return $this->canViewSupervisions($subject, $token);

            case self::Add:
                return $this->canAdd($token);

            case self::Edit:
            case self::Unplan:
                return $this->canEdit($subject, $token);

            case self::Remove:
                return $this->canRemove($subject, $token);

            case self::Manage:
                return $this->canManage($token);
        }

        throw new LogicException('This code should not be reached.');
    }

    private function getUserType(TokenInterface $token): ?UserType {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return null;
        }

        return $user->getUserType();
    }

    private function isStudentOrParent(TokenInterface $token): bool {
        $userType = $this->getUserType($token);

        if($userType === null) {
            return false;
        }

        return $userType->equals(UserType::Student()) || $userType->equals(UserType::Parent());
    }

    public function canAdd(TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, [ 'ROLE_EXAMS_CREATOR' ]);
    }

    public function canEdit(Exam $exam, TokenInterface $token): bool {
        if($this->accessDecisionManager->decide($token, ['ROLE_EXAMS_ADMIN']) === true) {
            return true;
        }

        if($exam->getExternalId() !== null) {
            // Non-Admins cannot edit external exams
            return false;
        }

        if($this->accessDecisionManager->decide($token, ['ROLE_EXAMS_CREATOR'])) {
            return true;
        }

        if($exam->isTuitionTeachersCanEditExam() !== true) {
            // Non-Admins cannot edit this exam
            return false;
        }

        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        $teacher = $user->getTeacher();

        if($teacher === null) {
            return false;
        }

        /** @var Tuition $tuition */
        foreach($exam->getTuitions() as $tuition) {
            foreach($tuition->getTeachers() as $tuitionTeacher) {
                if ($tuitionTeacher->getId() === $teacher->getId()) {
                    return true;
                }
            }
        }

        return false;
    }

    public function canManage(TokenInterface $token): bool {
        if($this->accessDecisionManager->decide($token, ['ROLE_EXAMS_ADMIN']) === true || $this->accessDecisionManager->decide($token, [ 'ROLE_EXAMS_CREATOR'])) {
            return true;
        }

        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        $teacher = $user->getTeacher();

        if($teacher === null) {
            return false;
        }

        return true;
    }

    public function canRemove(Exam $exam, TokenInterface $token): bool {
        if($this->accessDecisionManager->decide($token, ['ROLE_EXAMS_ADMIN']) === true) {
            return true;
        }

        if($exam->getExternalId() !== null) {
            // Non-Admins cannot edit external exams
            return false;
        }

        return $this->accessDecisionManager->decide($token, ['ROLE_EXAMS_CREATOR']) === true;
    }

    public function canViewExam(Exam $exam, TokenInterface $token): bool {
        if($this->accessDecisionManager->decide($token, ['ROLE_EXAMS_ADMIN']) === true || $this->accessDecisionManager->decide($token, ['ROLE_EXAMS_CREATOR']) === true) {
            return true;
        }

        $userType = $this->getUserType($token);

        if($userType === null) {
            return false;
        }

        if($this->examSettings->isVisibileFor($userType) === false) {
            return false;
        }

        $user = $token->getUser();
        $section = $this->sectionResoler->getSectionForDate($exam->getDate());

        if($user instanceof User && $this->isStudentOrParent($token)) {
            $visibleGradeIds = $this->examSettings->getVisibleGradeIds();
            $gradeIds = [ ];

            /** @var Student $student */
            foreach($user->getStudents() as $student) {
                $grade = $student->getGrade($section);

                if($grade !== null) {
                    $gradeIds[] = $grade->getId();
                }
            }

            if(count(array_intersect($visibleGradeIds, $gradeIds)) === 0) {
                return false;
            }
        }

        if($this->isStudentOrParent($token) && $exam->getDate() === null) {
            // Exam is not planned yet -> do not display
            return false;
        }

        $days = $this->examSettings->getTimeWindowForStudents();
        if($this->isStudentOrParent($token) && $days > 0) {
            $threshold = $this->dateHelper->getToday()
                ->modify(sprintf('+%d days', $days));

            return $exam->getDate() < $threshold;
        }

        return true;
    }

    public function canViewSupervisions(Exam $exam, TokenInterface $token): bool {
        $days = $this->examSettings->getTimeWindowForStudentsToSeeSupervisions();
        if($this->isStudentOrParent($token) && $days > 0) {
            $threshold = $this->dateHelper->getToday()
                ->modify(sprintf('+%d days', $days));

            return $exam->getDate() < $threshold;
        }

        return true;
    }

    private function canViewDetails(Exam $exam, TokenInterface $token): bool {
        return $this->isStudentOrParent($token) === false;
    }
}