<?php

namespace App\Repository;

use App\Entity\BookIntegrityCheckViolation;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\Teacher;
use DateTime;

interface BookIntegrityCheckViolationRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @param Student $student
     * @param DateTime $start
     * @param DateTime $end
     * @return BookIntegrityCheckViolation[]
     */
    public function findAllByStudent(Student $student, DateTime $start, DateTime $end): array;

    /**
     * @param Teacher $teacher
     * @param DateTime $start
     * @param DateTime $end
     * @return BookIntegrityCheckViolation[]
     */
    public function findAllByTeacher(Teacher $teacher, DateTime $start, DateTime $end): array;

    public function persist(BookIntegrityCheckViolation $violation): void;

    public function remove(BookIntegrityCheckViolation $violation): void;
}