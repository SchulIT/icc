<?php

namespace App\Security\Voter;

use App\Entity\Appointment;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupMembership;
use App\Entity\User;
use App\Entity\UserType;
use App\Utils\EnumArrayUtils;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AppointmentVoter extends Voter {

    const New = 'new-appointment';
    const Edit = 'edit';
    const Remove = 'remove';
    const View = 'view';

    private $accessDecisionManager;

    public function __construct(AccessDecisionManagerInterface $accessDecisionManager) {
        $this->accessDecisionManager = $accessDecisionManager;
    }

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject) {
        $attributes = [
            static::Edit,
            static::Remove,
            static::View
        ];

        return $attribute === static::New ||
              (in_array($attribute, $attributes) && $subject instanceof Appointment);
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
        switch ($attribute) {
            case static::New:
                return $this->canCreate($token);

            case static::Edit:
                return $this->canEdit($subject, $token);

            case static::Remove:
                return $this->canRemove($subject, $token);

            case static::View:
                return $this->canView($subject, $token);
        }

        throw new \LogicException('This code should be reached.');
    }

    private function canCreate(TokenInterface $token) {
        return $this->accessDecisionManager->decide($token, ['ROLE_APPOINTMENTS_ADMIN']);
    }

    private function canEdit(Appointment $appointment, TokenInterface $token) {
        return $this->accessDecisionManager->decide($token, ['ROLE_APPOINTMENTS_ADMIN']);
    }

    private function canRemove(Appointment $appointment, TokenInterface $token) {
        return $this->accessDecisionManager->decide($token, ['ROLE_APPOINTMENTS_ADMIN']);
    }

    private function canView(Appointment $appointment, TokenInterface $token) {
        if($this->accessDecisionManager->decide($token, ['ROLE_APPOINTMENTS_ADMIN']) || $this->accessDecisionManager->decide($token, [ 'ROLE_KIOSK' ])) {
            return true;
        }

        /** @var User $user */
        $user = $token->getUser();

        $isStudentOrParent = EnumArrayUtils::inArray($user->getUserType(), [ UserType::Student(), UserType::Parent() ]);

        if($isStudentOrParent !== true) {
            return true;
        }

        $appointmentStudyGroupsIds = $appointment->getStudyGroups()
            ->map(function(StudyGroup $studyGroup) {
                return $studyGroup->getId();
            })
            ->toArray();

        /** @var Student[] $students */
        $students = $user->getStudents();

        foreach($students as $student) {
            $studentStudyGroupsIds = $student->getStudyGroupMemberships()
                ->map(function(StudyGroupMembership $membership) {
                    return $membership->getStudyGroup()->getId();
                })
                ->toArray();

            $intersection = array_intersect($appointmentStudyGroupsIds, $studentStudyGroupsIds);

            if(count($intersection) > 0) {
                return true;
            }
        }

        return false;
    }
}