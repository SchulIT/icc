<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\LessonEntry;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\Tuition;
use DateTime;

interface LessonEntryRepositoryInterface {

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

    public function persist(LessonEntry $entry): void;

    public function remove(LessonEntry $entry): void;
}