<?php

namespace App\Dashboard\Absence;

use App\Dashboard\AbsentStudent;
use App\Entity\Student;
use DateTime;

interface AbsenceResolveStrategyInterface {
    /**
     * @param Student[] $students
     * @return AbsentStudent[]
     */
    public function resolveAbsentStudents(DateTime $dateTime, int $lesson, iterable $students): array;
}