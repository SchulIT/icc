<?php

namespace App\ParentsDay;

use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use SchulIT\CommonBundle\Helper\DateHelper;

class InvolvedUsersResolver {
    public function __construct(private readonly UserRepositoryInterface $userRepository,
                                private readonly DateHelper $dateHelper) {

    }

    /**
     * @param Student $student
     * @param TeacherItem[] $teachers
     * @param User|null $exclude
     * @return User[]
     */
    public function resolveUsers(Student $student, array $teachers, User|null $exclude = null): array {
        $users = array_merge(
            $this->getTeacherUsers($teachers),
            $this->getParentsUsers($student),
            $this->getStudentUsersIfFullyAged($student)
        );

        $result = [ ];

        foreach($users as $user) {
            if($user->getId() !== $exclude?->getId()) {
                $result[] = $user;
            }
        }

        return $result;
    }

    /**
     * @param TeacherItem[] $teachers
     * @return User[]
     */
    private function getTeacherUsers(array $teachers): array {
        return $this->userRepository->findAllTeachers($teachers);
    }

    /**
     * @param Student $student
     * @return User[]
     */
    private function getParentsUsers(Student $student): array {
        return $this->userRepository->findAllParentsByStudents([$student]);
    }

    /**
     * @param Student $student
     * @return User[]
     */
    private function getStudentUsersIfFullyAged(Student $student): array {
        if(!$student->isFullAged($this->dateHelper->getToday())) {
            return [ ];
        }

        return $this->userRepository->findAllStudentsByStudents([$student]);
    }
}