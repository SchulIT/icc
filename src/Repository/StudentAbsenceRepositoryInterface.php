<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\StudentAbsence;
use App\Entity\StudentAbsenceType;
use DateTime;
use Doctrine\ORM\Tools\Pagination\Paginator;

interface StudentAbsenceRepositoryInterface {
    /**
     * Returns all sick notes for the given students
     *
     * @param Student[] $students
     * @param StudentAbsenceType|null $type
     * @param DateTime|null $date
     * @param int|null $lesson
     * @return StudentAbsence[]
     */
    public function findByStudents(array $students, ?StudentAbsenceType $type = null, ?DateTime $date = null, ?int $lesson = null): array;

    /**
     * Returns a paginator which paginates on all students (e.g. for a given teacher) which are sick on the
     * specified date $date.
     *
     * @param Student[] $students
     * @param DateTime $date The current date
     * @param StudentAbsenceType|null $type
     * @param int $itemsPerPage
     * @param int $page
     * @return Paginator
     */
    public function getStudentsPaginator(array $students, DateTime $date, ?StudentAbsenceType $type, int $itemsPerPage, int &$page): Paginator;

    public function getStudentPaginator(Student $student, ?StudentAbsenceType $type, int $itemsPerPage, int &$page): Paginator;

    public function getGradePaginator(Grade $grade, Section $section, ?StudentAbsenceType $type, int $itemsPerPage, int &$page): Paginator;

    public function getPaginator(?StudentAbsenceType $type, int $itemsPerPage, int &$page): Paginator;

    /**
     * @param DateTime $start
     * @param DateTime $end
     * @return string[]
     */
    public function findAllUuids(DateTime $start, DateTime $end): array;

    /**
     * @param DateTime $threshold
     * @return int Number of removed sick notes
     */
    public function removeExpired(DateTime $threshold): int;

    /**
     * Removes all absences within the given timespan (start and end inclusive)
     *
     * @param DateTime $start
     * @param DateTime $end
     * @return int Number of removed absences
     */
    public function removeRange(DateTime $start, DateTime $end): int;

    public function persist(StudentAbsence $note): void;

    public function remove(StudentAbsence $note): void;
}