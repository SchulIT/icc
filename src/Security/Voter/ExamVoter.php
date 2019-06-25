<?php

namespace App\Security\Voter;

use App\Entity\Exam;
use App\Entity\User;
use App\Entity\UserType;
use App\Settings\ExamSettings;
use SchoolIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ExamVoter extends Voter {

    public const SHOW = 'show';
    public const INVIGILATORS = 'invigilators';
    public const DETAILS = 'details';

    private $dateHelper;
    private $examSettings;

    public function __construct(DateHelper $dateHelper, ExamSettings $examSettings) {
        $this->dateHelper = $dateHelper;
        $this->examSettings = $examSettings;
    }

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject) {
        $attributes = [
            static::DETAILS,
            static::INVIGILATORS,
            static::SHOW
        ];

        return $subject instanceof Exam
            && in_array($attribute, $attributes);
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
        switch($attribute) {
            case static::SHOW:
                return $this->canViewExam($subject, $token);

            case static::DETAILS:
                return $this->canViewDetails($subject, $token);

            case static::INVIGILATORS:
                return $this->canViewInvigilators($subject, $token);
        }

        throw new \LogicException('This code should not be reached.');
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

    public function canViewExam(Exam $exam, TokenInterface $token): bool {
        $userType = $this->getUserType($token);

        if($userType === null) {
            return false;
        }

        if($this->examSettings->isVisibileFor($userType) === false) {
            return false;
        }

        $days = $this->examSettings->getTimeWindowForStudents();
        if($this->isStudentOrParent($token) && $days > 0) {
            dump('student');

            $threshold = $this->dateHelper->getToday()
                ->modify(sprintf('+%d days', $days));

            return $exam->getDate() < $threshold;
        }

        return true;
    }

    public function canViewInvigilators(Exam $exam, TokenInterface $token): bool {
        $days = $this->examSettings->getTimeWindowForStudentsToSeeInvigilators();
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