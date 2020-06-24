<?php

namespace App\Message;

use App\Entity\Message;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\User;
use App\Entity\UserType;
use App\Entity\UserTypeEntity;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Utils\ArrayUtils;
use App\Utils\EnumArrayUtils;
use Doctrine\Common\Collections\Collection;

abstract class AbstractMessageFileViewHelper {
    private $studentRepository;
    private $teacherRepository;
    private $userRepository;

    public function __construct(StudentRepositoryInterface $studentRepository, TeacherRepositoryInterface $teacherRepository, UserRepositoryInterface $userRepository) {
        $this->studentRepository = $studentRepository;
        $this->teacherRepository = $teacherRepository;
        $this->userRepository = $userRepository;
    }

    public function createView(Message $message): AbstractMessageFileView {
        /** @var UserType[] $visibilities */
        $visibilities = $this->getUserTypes($message)->map(function(UserTypeEntity $visibility) {
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
            $students = $this->studentRepository->findAllByStudyGroups($this->getStudyGroups($message)->toArray());

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

        /** @var UserType[] $remainingUserTypes */
        $remainingUserTypes = EnumArrayUtils::remove($visibilities,
            [
                UserType::Student(),
                UserType::Teacher(),
                UserType::Parent()
            ]);

        $users = ArrayUtils::createArrayWithKeys(
            $this->userRepository->findAllByUserTypes($remainingUserTypes),
            function(User $user) {
                return $user->getId();
            }
        );

        return $this->createViewFromData($message, $students, $studentUsersLookup, $parentUsersLookup, $teachers, $teacherUsersLookup, $users);
    }

    /**
     * @param Message $message
     * @return Collection<UserTypeEntity>
     */
    protected abstract function getUserTypes(Message $message): Collection;

    /**
     * @param Message $message
     * @return Collection<StudyGroup>
     */
    protected abstract function getStudyGroups(Message $message): Collection;

    protected abstract function createViewFromData(Message $message, array $students, array $studentUsersLookup, array $parentUsersLookup, array $teachers, array $teacherUsersLookup, array $users): AbstractMessageFileView;
}