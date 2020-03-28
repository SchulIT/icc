<?php

namespace App\Message;

use App\Entity\Message;
use App\Entity\MessageFile;
use App\Entity\MessageFileUpload;
use App\Entity\MessageVisibility;
use App\Entity\Student;
use App\Entity\User;
use App\Entity\UserType;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Utils\ArrayUtils;
use App\Utils\EnumArrayUtils;

class MessageFileUploadViewHelper {
    private $studentRepository;
    private $teacherRepository;
    private $userRepository;

    public function __construct(StudentRepositoryInterface $studentRepository, TeacherRepositoryInterface $teacherRepository, UserRepositoryInterface $userRepository) {
        $this->studentRepository = $studentRepository;
        $this->teacherRepository = $teacherRepository;
        $this->userRepository = $userRepository;
    }

    public function createView(Message $message): MessageFileUploadView {
        /** @var UserType[] $visibilities */
        $visibilities = $message->getVisibilities()->map(function(MessageVisibility $visibility) {
            return $visibility->getUserType();
        });

        $students = [ ];
        $studentUsersLookup = [ ];
        $parentUsersLookup = [ ];
        $teachers = [ ];
        $teacherUsersLookup = [ ];

        if(EnumArrayUtils::inArray(UserType::Teacher(), $visibilities)) {
            $teachers = $this->teacherRepository->findAll();
            $teacherUsersLookup = ArrayUtils::createArrayWithKeys(
                $this->userRepository->findAllTeachers($teachers),
                function(User $user) {
                    return $user->getTeacher()->getId();
                },
                true
            );
        }

        if(EnumArrayUtils::inArray(UserType::Student(), $visibilities) || EnumArrayUtils::inArray(UserType::Parent(), $visibilities)) {
            $students = $this->studentRepository->findAllByStudyGroups($message->getStudyGroups()->toArray());

            $studentUsersLookup = ArrayUtils::createArrayWithKeys(
                $this->userRepository->findAllStudentsByStudents($students),
                function(User $user) {
                    return $user->getStudents()->map(function(Student $student) {
                        return $student->getId();
                    });
                },
                true
            );

            $parentUsersLookup = ArrayUtils::createArrayWithKeys(
                $this->userRepository->findAllParentsByStudents($students),
                function(User $user) {
                    return $user->getStudents()->map(function(Student $student) {
                        return $student->getId();
                    });
                },
                true
            );
        }

        $users = ArrayUtils::createArrayWithKeys(
            $this->userRepository->findAllByUserTypes(
                EnumArrayUtils::remove($visibilities,
                    [
                        UserType::Student(),
                        UserType::Teacher(),
                        UserType::Parent()
                    ])
            ),
            function(User $user) {
                return $user->getId();
            }
        );

        return new MessageFileUploadView(
            $students,
            $studentUsersLookup,
            $parentUsersLookup,
            $teachers,
            $teacherUsersLookup,
            $users,
            $this->getUploads($message)
        );
    }

    /**
     * Returns the uploads for all users.
     *
     * @param Message $message
     * @return array<int, MessageFileUpload[]> Key is the user's ID
     */
    private function getUploads(Message $message): array {
        $uploads = [ ];

        /** @var MessageFile $file */
        foreach($message->getFiles() as $file) {
            /** @var MessageFileUpload $upload */
            foreach($file->getUploads() as $upload) {
                if(!isset($uploads[$upload->getUser()->getId()])) {
                    $uploads[$upload->getUser()->getId()] = [ ];
                }

                $uploads[$upload->getUser()->getId()][] = $upload;
            }
        }

        return $uploads;
    }
}