<?php

namespace App\Book\Repository;

use App\Common\Entity\Grade;
use App\Book\Entity\LessonEntry;
use App\Common\Entity\Student;
use App\Common\Entity\Teacher;
use App\Common\Entity\Tuition;
use DateTime;

interface LessonEntryRepositoryInterface {

    public function findLastByTuition(Tuition $tuition, DateTime $today): ?LessonEntry;

    /**
     * @param Tuition $tuition
     * @param DateTime $start
     * @param DateTime $end
     * @return LessonEntry[]
     */
    public function findAllByTuition(Tuition $tuition, DateTime $start, DateTime $end): array;

    /**
     * @param Teacher $teacher
     * @param DateTime $start
     * @param DateTime $end
     * @return LessonEntry[]
     */
    public function findAllBySubstituteTeacher(Teacher $teacher, DateTime $start, DateTime $end): array;

    /**
     * @param Grade $grade
     * @param DateTime $start
     * @param DateTime $end
     * @return LessonEntry[]
     */
    public function findAllByGrade(Grade $grade, DateTime $start, DateTime $end): array;

    /**
     * @param Grade $grade
     * @param DateTime $start
     * @param DateTime $end
     * @return LessonEntry[]
     */
    public function findAllByGradeWithExercises(Grade $grade, DateTime $start, DateTime $end): array;

    /**
     * @param Student $student
     * @param DateTime $start
     * @param DateTime $end
     * @return LessonEntry[]
     */
    public function findAllByStudentWithExercises(Student $student, DateTime $start, DateTime $end): array;

    /**
     * @param Student $student
     * @param DateTime $start
     * @param DateTime $end
     * @return LessonEntry[]
     */
    public function findAllByStudents(Student $student, DateTime $start, DateTime $end): array;

    public function persist(LessonEntry $entry): void;

    public function remove(LessonEntry $entry): void;
}