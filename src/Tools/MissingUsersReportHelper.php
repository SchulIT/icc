<?php

namespace App\Tools;

use App\Common\Entity\Student;
use App\Common\Entity\User;
use App\Common\Repository\UserRepositoryInterface;
use App\Framework\Utils\ArrayUtils;

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