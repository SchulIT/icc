<?php

namespace App\Message;

use App\Entity\Message;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupMembership;
use App\Entity\User;
use App\Entity\UserTypeEntity;
use App\Repository\UserRepositoryInterface;
use App\Utils\ArrayUtils;

readonly class MessageRecipientResolver {

    public function __construct(private UserRepositoryInterface $userRepository)
    {
    }

    public function resolveRecipients(Message $message): array {
        // Get users that have message notifications enabled
        $users = $this->userRepository->findAllByNotifyMessages();

        $userTypes = $message->getVisibilities()->map(fn(UserTypeEntity $entity) => $entity->getUserType())->toArray();

        $studyGroupIds = $message->getStudyGroups()->map(fn(StudyGroup $studyGroup) => $studyGroup->getId())->toArray();

        $users = array_filter(
            $users,
            function(User $user) use($userTypes, $studyGroupIds) {
                // Filter users that matches message visibility
                if(ArrayUtils::inArray($user->getUserType(), $userTypes) === false) {
                    return false;
                }

                // We only need further checks for students and parents
                if($user->isStudentOrParent() === false) {
                    return true;
                }

                $studentStudyGroupIds = [ ];

                /** @var Student $student */
                foreach($user->getStudents() as $student) {
                    /** @var StudyGroupMembership $membership */
                    foreach($student->getStudyGroupMemberships() as $membership) {
                        $studentStudyGroupIds[] = $membership->getStudyGroup()->getId();
                    }
                }

                $intersection = array_intersect($studyGroupIds, $studentStudyGroupIds);

                return count($intersection) > 0;
            }
        );

        return array_values($users);
    }
}