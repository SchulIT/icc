<?php

namespace App\Book\Repository;

use App\Book\Entity\BookIntegrityCheckViolation;
use App\Common\Entity\Grade;
use App\Common\Entity\Section;
use App\Common\Entity\Student;
use App\Common\Entity\StudyGroup;
use App\Common\Entity\Teacher;
use App\Framework\Repository\TransactionalRepositoryInterface;
use DateTime;

interface BookIntegrityCheckViolationRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @param Student $student
     * @param DateTime $start
     * @param DateTime $end
     * @return BookIntegrityCheckViolation[]
     */
    public function findAllByStudent(Student $student, DateTime $start, DateTime $end): array;

    public function countAllByStudents(array $students, DateTime $start, DateTime $end): int;

    /**
     * @param Teacher $teacher
     * @param DateTime $start
     * @param DateTime $end
     * @return BookIntegrityCheckViolation[]
     */
    public function findAllByTeacher(Teacher $teacher, DateTime $start, DateTime $end): array;

    public function countAllByTeacher(Teacher $teacher, DateTime $start, DateTime $end): int;

    public function persist(BookIntegrityCheckViolation $violation): void;

    public function remove(BookIntegrityCheckViolation $violation): void;
}