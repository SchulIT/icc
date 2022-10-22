<?php

namespace App\Message;

use App\Entity\Message;
use App\Entity\MessageConfirmation;
use App\Entity\UserType;
use App\Entity\UserTypeEntity;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Utils\ArrayUtils;
use App\Utils\EnumArrayUtils;

class MessageConfirmationViewHelper {

    public function __construct(private StudentRepositoryInterface $studentRepository, private TeacherRepositoryInterface $teacherRepository, private UserRepositoryInterface $userRepository)
    {
    }

    public function createView(Message $message): MessageConfirmationView {
        /** @var UserType[] $visibilities */
        $visibilities = $message->getConfirmationRequiredUserTypes()->map(fn(UserTypeEntity $visibility) => $visibility->getUserType());

        $students = [ ];
        $teachers = [ ];

        if(EnumArrayUtils::inArray(UserType::Teacher(), $visibilities)) {
            $teachers = $this->teacherRepository->findAll();
        }

        if(EnumArrayUtils::inArray(UserType::Student(), $visibilities) || EnumArrayUtils::inArray(UserType::Parent(), $visibilities)) {
            $students = $this->studentRepository->findAllByStudyGroups($message->getConfirmationRequiredStudyGroups()->toArray());
        }

        /** @var UserType[] $remainingUserTypes */
        $remainingUserTypes = EnumArrayUtils::remove($visibilities,
            [
                UserType::Student(),
                UserType::Teacher(),
                UserType::Parent()
            ]);

        $users = $this->userRepository->findAllByUserTypes($remainingUserTypes);

        return new MessageConfirmationView(
            $students,
            $this->getStudentConfirmations($message),
            $this->getParentConfirmations($message),
            $teachers,
            $this->getTeacherConfirmations($message),
            $users,
            $this->getUserConfirmations($message)
        );
    }

    /**
     * Returns all student confirmations. Keys are the student ids.
     *
     * @return MessageConfirmation[]
     */
    private function getStudentConfirmations(Message $message): array {
        $confirmations = $message->getConfirmations()
            ->filter(fn(MessageConfirmation $confirmation) => $confirmation->getUser()->getUserType()->equals(UserType::Student()) && $confirmation->getUser()->getStudents()->first() !== null)->toArray();

        return ArrayUtils::createArrayWithKeys($confirmations, fn(MessageConfirmation $confirmation) => $confirmation->getUser()->getStudents()->first()->getId());
    }

    /**
     * Returns all parent confirmations. Keys are the student ids.
     *
     * @return MessageConfirmation[]
     */
    private function getParentConfirmations(Message $message): array {
        /** @var MessageConfirmation[] $confirmedParentUsers */
        $confirmedParentUsers = $message->getConfirmations()
            ->filter(fn(MessageConfirmation $confirmation) => $confirmation->getUser()->getUserType()->equals(UserType::Parent()));

        $confirmations = [ ];

        foreach($confirmedParentUsers as $confirmedParentUser) {
            foreach($confirmedParentUser->getUser()->getStudents() as $student) {
                $confirmations[$student->getId()] = $confirmedParentUser;
            }
        }

        return $confirmations;
    }

    /**
     * Returns all teacher confirmations. Keys are the teacher ids.
     *
     * @return MessageConfirmation[]
     */
    private function getTeacherConfirmations(Message $message): array {
        $confirmedTeachers = $message->getConfirmations()
            ->filter(fn(MessageConfirmation $confirmation) => $confirmation->getUser()->getUserType()->equals(UserType::Teacher()) && $confirmation->getUser()->getTeacher() !== null)->toArray();

        return ArrayUtils::createArrayWithKeys($confirmedTeachers, fn(MessageConfirmation $confirmation) => $confirmation->getUser()->getTeacher()->getId());
    }

    /**
     * Returns all normal user (non-student/-parent/-teacher users) confirmations. Keys are the user ids.
     *
     * @return MessageConfirmation[]
     */
    private function getUserConfirmations(Message $message): array {
        $confirmedUsers = $message->getConfirmations()
            ->filter(fn(MessageConfirmation $confirmation) => !EnumArrayUtils::inArray($confirmation->getUser()->getUserType(), [
                UserType::Student(),
                UserType::Parent(),
                UserType::Teacher()
            ]))->toArray();

        return ArrayUtils::createArrayWithKeys($confirmedUsers, fn(MessageConfirmation $confirmation) => $confirmation->getUser()->getId());
    }
}