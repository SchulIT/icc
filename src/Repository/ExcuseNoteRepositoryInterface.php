<?php

namespace App\Repository;

use App\Entity\ExcuseNote;
use App\Entity\Student;
use DateTime;
use Doctrine\ORM\Tools\Pagination\Paginator;

interface ExcuseNoteRepositoryInterface {

    /**
     * @param Student[] $students
     * @param DateTime $date
     * @return ExcuseNote[]
     */
    public function findByStudentsAndDate(array $students, DateTime $date): array;

    /**
     * @param int $itemsPerPage
     * @param int $page
     * @param Student|null $student
     * @param DateTime $start
     * @param DateTime $end
     * @return Paginator
     */
    public function getPaginator(int $itemsPerPage, int &$page, ?Student $student, DateTime $start, DateTime $end): Paginator;

    /**
     * @param Student $student
     * @return ExcuseNote[]
     */
    public function findByStudent(Student $student): array;

    /**
     * @param Student[] $students
     * @return ExcuseNote[]
     */
    public function findByStudents(array $students): array;

    public function persist(ExcuseNote $note): void;

    public function remove(ExcuseNote $note): void;

    public function removeBetween(DateTime $start, DateTime $end): int;
}