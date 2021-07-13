<?php

namespace App\Repository;

use App\Entity\LessonAttendance;
use App\Entity\Student;
use DateTime;

interface LessonAttendanceRepositoryInterface {

    /**
     * @param Student[] $students
     * @param DateTime $dateTime
     * @return LessonAttendance[]
     */
    public function findAbsentByStudents(array $students, DateTime $dateTime): array;

    public function persist(LessonAttendance $attendance): void;

    public function remove(LessonAttendance $attendance): void;
}