<?php

namespace App\Repository;

use App\Entity\DateLesson;
use App\Entity\Attendance;
use App\Entity\LessonEntry;
use App\Entity\Student;
use App\Entity\Tuition;
use DateTime;

interface LessonAttendanceRepositoryInterface {

    public function countAbsent(LessonEntry $entry): int;

    public function countPresent(LessonEntry $entry): int;

    public function countLate(LessonEntry $entry): int;

    /**
     * @param Student $student
     * @param DateTime $start
     * @param DateTime $end
     * @param Tuition[] $tuitions
     * @return Attendance[]
     */
    public function findByStudent(Student $student, DateTime $start, DateTime $end, array $tuitions = [ ]): array;

    /**
     * @param Student $student
     * @param DateTime $start
     * @param DateTime $end
     * @return Attendance[]
     */
    public function findByStudentEvents(Student $student, DateTime $start, DateTime $end): array;

    /**
     * @param Student $student
     * @param DateTime $start
     * @param DateTime $end
     * @param Tuition[] $tuitions
     * @return Attendance[]
     */
    public function findLateByStudent(Student $student, DateTime $start, DateTime $end, array $tuitions = [ ]): array;

    /**
     * @param Student $student
     * @param DateTime $start
     * @param DateTime $end
     * @param Tuition[] $tuitions
     * @return Attendance[]
     */
    public function findAbsentByStudent(Student $student, DateTime $start, DateTime $end, array $tuitions = [ ]): array;

    /**
     * @param Student[] $students
     * @param Tuition[] $tuitions
     * @return Attendance[]
     */
    public function findAbsentByStudents(array $students, DateTime $start, DateTime $end, array $tuitions = [ ]): array;

    /**
     * @param Student[] $students
     * @param DateTime $dateTime
     * @return Attendance[]
     */
    public function findAbsentByStudentsAndDate(array $students, DateTime $dateTime): array;

    /**
     * @param Student $student
     * @param DateTime $start
     * @param DateTime $end
     * @return Attendance[]
     * @deprecated Use findByStudent with parameter tuitions = [ ]
     */
    public function findByStudentAndDateRange(Student $student, DateTime $start, DateTime $end): array;

    public function persist(Attendance $attendance): void;

    public function remove(Attendance $attendance): void;
}