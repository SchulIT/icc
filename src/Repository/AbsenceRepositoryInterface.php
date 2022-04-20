<?php

namespace App\Repository;

use App\Entity\Absence;
use App\Entity\Student;
use DateTime;

interface AbsenceRepositoryInterface extends TransactionalRepositoryInterface {
    public function findAll(): array;

    /**
     * Returns absent teachers for the given date.
     *
     * @param \DateTime $date
     * @return Absence[]
     */
    public function findAllTeachers(\DateTime $date): array;

    /**
     * Returns absent study groups for the given date.
     *
     * @param \DateTime $date
     * @return Absence[]
     */
    public function findAllStudyGroups(\DateTime $date): array;

    /**
     * @param \DateTime $dateTime
     * @param Student[] $students
     * @param int $lesson
     * @return Student[]
     */
    public function findAllStudentsByDateAndLesson(\DateTime $dateTime, array $students, int $lesson);

    public function persist(Absence $person): void;

    public function removeAll(?DateTime $dateTime = null): void;
}