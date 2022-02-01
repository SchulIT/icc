<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\SickNote;
use App\Entity\SickNoteReason;
use App\Entity\Student;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\Tools\Pagination\Paginator;

interface SickNoteRepositoryInterface {
    /**
     * Returns all sick notes for the given students
     *
     * @param Student[] $students
     * @param SickNoteReason|null $reason
     * @param DateTime|null $date
     * @param int|null $lesson
     * @return SickNote[]
     */
    public function findByStudents(array $students, ?SickNoteReason $reason = null, ?DateTime $date = null, ?int $lesson = null): array;

    /**
     * Returns a paginator which paginates on all students (e.g. for a given teacher) which are sick on the
     * specified date $date.
     *
     * @param Student[] $students
     * @param DateTime $date The current date
     * @param SickNoteReason|null $reason
     * @param int $itemsPerPage
     * @param int $page
     * @return Paginator
     */
    public function getStudentsPaginator(array $students, DateTime $date, ?SickNoteReason $reason, int $itemsPerPage, int &$page): Paginator;

    public function getStudentPaginator(Student $student, ?SickNoteReason $reason, int $itemsPerPage, int &$page): Paginator;

    public function getGradePaginator(Grade $grade, Section $section, ?SickNoteReason $reason, int $itemsPerPage, int &$page): Paginator;

    /**
     * @param DateTime $threshold
     * @return int Number of removed sick notes
     */
    public function removeExpired(DateTime $threshold): int;

    public function persist(SickNote $note): void;

    public function remove(SickNote $note): void;
}