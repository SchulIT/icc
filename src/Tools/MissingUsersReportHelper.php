<?php

namespace App\Tools;

use App\Entity\Student;
use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use App\Utils\ArrayUtils;

class MissingUsersReportHelper {

    public function __construct(private readonly UserRepositoryInterface $userRepository) {

    }

    /**
     * @param Student[] $students
     * @return Student[]
     */
    public function getMissingStudents(array $students): array {
        $users = ArrayUtils::createArrayWithKeys(
            $this->userRepository->findAllStudentsByStudents($students),
            fn(User $user) => $user->getStudents()->map(fn(Student $student) => $student->getId())->toArray(),
            true
        );
        $missing = [ ];

        foreach($students as $student) {
            if(!array_key_exists($student->getId(), $users)) {
                $missing[] = $student;
            }
        }

        return $missing;
    }

    /**
     * @param Student[] $students
     * @return Student[]
     */
    public function getMissingParents(array $students): array {
        $users = ArrayUtils::createArrayWithKeys(
            $this->userRepository->findAllParentsByStudents($students),
            fn(User $user) => $user->getStudents()->map(fn(Student $student) => $student->getId())->toArray(),
            true
        );
        $missing = [ ];

        foreach($students as $student) {
            if(!array_key_exists($student->getId(), $users)) {
                $missing[] = $student;
            }
        }

        return $missing;
    }
}