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

    private $studentRepository;
    private $teacherRepository;
    private $userRepository;

    public function __construct(StudentRepositoryInterface $studentRepository, TeacherRepositoryInterface $teacherRepository, UserRepositoryInterface $userRepository) {
        $this->studentRepository = $studentRepository;
        $this->teacherRepository = $teacherRepository;
        $this->userRepository = $userRepository;
    }

    public function createView(Message $message): MessageConfirmationView {
        /** @var UserType[] $visibilities */
        $visibilities = $message->getVisibilities()->map(function(UserTypeEntity $visibility) {
            return $visibility->getUserType();
        });

        $students = [ ];
        $teachers = [ ];

        if(EnumArrayUtils::inArray(UserType::Teacher(), $visibilities)) {
            $teachers = $this->teacherRepository->findAll();
        }

        if(EnumArrayUtils::inArray(UserType::Student(), $visibilities) || EnumArrayUtils::inArray(UserType::Parent(), $visibilities)) {
            $students = $this->studentRepository->findAllByStudyGroups($message->getStudyGroups()->toArray());
        }

        $users = $this->userRepository->findAllByUserTypes(
            EnumArrayUtils::remove($visibilities,
                [
                    UserType::Student(),
                    UserType::Teacher(),
                    UserType::Parent()
                ])
        );

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
     * @param Message $message
     * @return MessageConfirmation[]
     */
    private function getStudentConfirmations(Message $message): array {
        $confirmations = $message->getConfirmations()
            ->filter(function(MessageConfirmation $confirmation) {
                return $confirmation->getUser()->getUserType()->equals(UserType::Student()) && $confirmation->getUser()->getStudents()->first() !== null;
            })->toArray();

        return ArrayUtils::createArrayWithKeys($confirmations, function(MessageConfirmation $confirmation) {
            return $confirmation->getUser()->getStudents()->first()->getId();
        });
    }

    /**
     * Returns all parent confirmations. Keys are the student ids.
     *
     * @param Message $message
     * @return MessageConfirmation[]
     */
    private function getParentConfirmations(Message $message): array {
        /** @var MessageConfirmation[] $confirmedParentUsers */
        $confirmedParentUsers = $message->getConfirmations()
            ->filter(function(MessageConfirmation $confirmation) {
                return $confirmation->getUser()->getUserType()->equals(UserType::Parent());
            });

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
     * @param Message $message
     * @return MessageConfirmation[]
     */
    private function getTeacherConfirmations(Message $message): array {
        $confirmedTeachers = $message->getConfirmations()
            ->filter(function(MessageConfirmation $confirmation) {
                return $confirmation->getUser()->getUserType()->equals(UserType::Teacher()) && $confirmation->getUser()->getTeacher() !== null;
            })->toArray();

        return ArrayUtils::createArrayWithKeys($confirmedTeachers, function(MessageConfirmation $confirmation) {
            return $confirmation->getUser()->getTeacher()->getId();
        });
    }

    /**
     * Returns all normal user (non-student/-parent/-teacher users) confirmations. Keys are the user ids.
     *
     * @param Message $message
     * @return MessageConfirmation[]
     */
    private function getUserConfirmations(Message $message): array {
        $confirmedUsers = $message->getConfirmations()
            ->filter(function (MessageConfirmation $confirmation) {
                return !EnumArrayUtils::inArray($confirmation->getUser()->getUserType(), [
                    UserType::Student(),
                    UserType::Parent(),
                    UserType::Teacher()
                ]);
            })->toArray();

        return ArrayUtils::createArrayWithKeys($confirmedUsers, function (MessageConfirmation $confirmation) {
            return $confirmation->getUser()->getId();
        });
    }
}