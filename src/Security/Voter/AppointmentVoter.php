<?php

namespace App\Security\Voter;

use App\Entity\Appointment;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupMembership;
use App\Entity\User;
use App\Entity\UserType;
use App\Settings\AppointmentsSettings;
use LogicException;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AppointmentVoter extends Voter {

    public const New = 'new-appointment';
    public const Edit = 'edit';
    public const Remove = 'remove';
    public const View = 'view';
    public const Confirm = 'confirm';

    public function __construct(private AppointmentsSettings $settings, private DateHelper $dateHelper, private AccessDecisionManagerInterface $accessDecisionManager)
    {
    }

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject): bool {
        $attributes = [
            self::Edit,
            self::Remove,
            self::View,
            self::Confirm
        ];

        return $attribute === self::New ||
              (in_array($attribute, $attributes) && $subject instanceof Appointment);
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token, Vote|null $vote = null): bool
    {
        return match ($attribute) {
            self::New => $this->canCreate($token),
            self::Edit => $this->canEdit($subject, $token),
            self::Remove => $this->canRemove($subject, $token),
            self::Confirm => $this->canConfirm($token),
            self::View => $this->canView($subject, $token),
            default => throw new LogicException('This code should be reached.'),
        };
    }

    private function canCreate(TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, ['ROLE_APPOINTMENT_CREATOR']);
    }

    private function canConfirm(TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, ['ROLE_APPOINTMENTS_ADMIN']);
    }

    private function canEdit(Appointment $appointment, TokenInterface $token): bool {
        if($this->accessDecisionManager->decide($token, ['ROLE_APPOINTMENTS_ADMIN']) === true) {
            return true;
        }

        if($appointment->getCreatedBy() === null) {
            return false;
        }

        /** @var User|null $user */
        $user = $token->getUser();

        if($user === null) {
            return false;
        }

        return $appointment->getCreatedBy()->getId() === $user->getId();
    }

    private function canRemove(Appointment $appointment, TokenInterface $token): bool {
        return $this->canEdit($appointment, $token);
    }

    private function canView(Appointment $appointment, TokenInterface $token): bool {
        if($this->accessDecisionManager->decide($token, ['ROLE_APPOINTMENTS_ADMIN']) || $this->accessDecisionManager->decide($token, [ 'ROLE_APPOINTMENT_VIEWER' ])) {
            return true;
        }

        /** @var User $user */
        $user = $token->getUser();

        $start = $this->settings->getStart($user->getUserType());
        $end = $this->settings->getEnd($user->getUserType());
        $today = $this->dateHelper->getToday();

        if($start === null || $start > $today || ($end !== null && $end < $today)) {
            return false;
        }

        $isStudentOrParent = $user->isStudentOrParent();

        if($isStudentOrParent !== true) {
            // Everyone but students and parents may pass
            return true;
        }

        // Check confirmation status (students and parents must not see non-confirmed appointments)
        if($appointment->isConfirmed() === false) {
            return false;
        }

        // Check visibility (students and parents only)
        if($this->checkVisibility($appointment, $user->getUserType()) !== true) {
            return false;
        }

        $appointmentStudyGroupsIds = $appointment->getStudyGroups()
            ->map(fn(StudyGroup $studyGroup) => $studyGroup->getId())
            ->toArray();

        /** @var Student[] $students */
        $students = $user->getStudents();

        foreach($students as $student) {
            $studentStudyGroupsIds = $student->getStudyGroupMemberships()
                ->map(fn(StudyGroupMembership $membership) => $membership->getStudyGroup()->getId())
                ->toArray();

            $intersection = array_intersect($appointmentStudyGroupsIds, $studentStudyGroupsIds);

            if(count($intersection) > 0) {
                return true;
            }
        }

        return false;
    }

    private function checkVisibility(Appointment $appointment, UserType $userType): bool {
        foreach($appointment->getVisibilities() as $visibility) {
            if($visibility->getUserType() === $userType) {
                return true;
            }
        }

        return false;
    }
}