<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\Lesson;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\Tuition;
use DateTime;
use Doctrine\ORM\Tools\Pagination\Paginator;

interface LessonRepositoryInterface extends TransactionalRepositoryInterface {

    public function countMissingByTeacher(Teacher $teacher, DateTime $start, DateTime $end): int;

    public function countMissingByGrade(Grade $grade, DateTime $start, DateTime $end): int;

    public function countMissingByTuition(Tuition $tuition, DateTime $start, DateTime $end): int;

    public function getMissingByTeacherPaginator(int $itemsPerPage, int &$page, Teacher $teacher, DateTime $start, DateTime $end): Paginator;

    public function getMissingByGradePaginator(int $itemsPerPage, int &$page, Grade $grade, DateTime $start, DateTime $end): Paginator;

    public function getMissingByTuitionPaginator(int $itemsPerPage, int &$page, Tuition $tuition, DateTime $start, DateTime $end): Paginator;

    public function countByDate(DateTime $start, DateTime $end): int;

    /**
     * @param DateTime $dateTime
     * @return Lesson[]
     */
    public function findAllByDate(DateTime $dateTime): array;

    /**
     * @param Tuition[] $tuitions
     * @param DateTime $start
     * @param DateTime $end
     * @return Lesson[]
     */
    public function findAllByTuitions(array $tuitions, DateTime $start, DateTime $end): array;

    public function persist(Lesson $lesson): void;

    public function remove(Lesson $lesson): void;

    public function removeBySection(Section $section): int;

    /**
     * @param Tuition[] $tuitions
     * @param Student|null $student
     * @return int
     */
    public function countHoldLessons(array $tuitions, ?Student $student): int;
}