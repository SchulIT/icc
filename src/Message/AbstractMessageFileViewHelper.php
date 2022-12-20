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
use Doctrine\Common\Collections\Collection;

abstract class AbstractMessageFileViewHelper {
    public function __construct(private StudentRepositoryInterface $studentRepository, private TeacherRepositoryInterface $teacherRepository, private UserRepositoryInterface $userRepository)
    {
    }

    public function createView(Message $message): AbstractMessageFileView {
        /** @var UserType[] $visibilities */
        $visibilities = $this->getUserTypes($message)->map(fn(UserTypeEntity $visibility) => $visibility->getUserType());

        $students = [ ];
        $studentUsersLookup = [ ];
        $parentUsersLookup = [ ];
        $teachers = [ ];
        $teacherUsersLookup = [ ];

        if(ArrayUtils::inArray(UserType::Teacher, $visibilities)) {
            $teachers = $this->teacherRepository->findAll();
            $teacherUsersLookup = ArrayUtils::createArrayWithKeys(
                $this->userRepository->findAllTeachers($teachers),
                fn(User $user) => $user->getTeacher()->getId(),
                true
            );
        }

        if(ArrayUtils::inArray(UserType::Student, $visibilities) || ArrayUtils::inArray(UserType::Parent, $visibilities)) {
            $students = $this->studentRepository->findAllByStudyGroups($this->getStudyGroups($message)->toArray());

            $studentUsersLookup = ArrayUtils::createArrayWithKeys(
                $this->userRepository->findAllStudentsByStudents($students),
                fn(User $user) => $user->getStudents()->map(fn(Student $student) => $student->getId()),
                true
            );

            $parentUsersLookup = ArrayUtils::createArrayWithKeys(
                $this->userRepository->findAllParentsByStudents($students),
                fn(User $user) => $user->getStudents()->map(fn(Student $student) => $student->getId()),
                true
            );
        }

        /** @var UserType[] $remainingUserTypes */
        $remainingUserTypes = ArrayUtils::remove($visibilities,
            [
                UserType::Student,
                UserType::Teacher,
                UserType::Parent
            ]);

        $users = ArrayUtils::createArrayWithKeys(
            $this->userRepository->findAllByUserTypes($remainingUserTypes),
            fn(User $user) => $user->getId()
        );

        return $this->createViewFromData($message, $students, $studentUsersLookup, $parentUsersLookup, $teachers, $teacherUsersLookup, $users);
    }

    /**
     * @return Collection<UserTypeEntity>
     */
    protected abstract function getUserTypes(Message $message): Collection;

    /**
     * @return Collection<StudyGroup>
     */
    protected abstract function getStudyGroups(Message $message): Collection;

    protected abstract function createViewFromData(Message $message, array $students, array $studentUsersLookup, array $parentUsersLookup, array $teachers, array $teacherUsersLookup, array $users): AbstractMessageFileView;
}