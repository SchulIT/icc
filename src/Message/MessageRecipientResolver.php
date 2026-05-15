<?php

namespace App\Message;

use App\Message\Entity\Message;
use App\Common\Entity\Student;
use App\Common\Entity\StudyGroup;
use App\Common\Entity\StudyGroupMembership;
use App\Common\Entity\User;
use App\Common\Entity\UserTypeEntity;
use App\Common\Repository\UserRepositoryInterface;
use App\Framework\Utils\ArrayUtils;

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