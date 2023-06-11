<?php

namespace App\Repository;

use App\Entity\Teacher;
use App\Entity\TeacherAbsence;
use App\Entity\TeacherAbsenceLesson;
use DateTime;
use Doctrine\ORM\Tools\Pagination\Paginator;

interface TeacherAbsenceRepositoryInterface {
    public function getPaginator(int $itemsPerPage, int &$page, bool $hideProcessed, ?DateTime $startDate, ?DateTime $endDate, ?Teacher $teacher = null): Paginator;

    public function persist(TeacherAbsence|TeacherAbsenceLesson $absenceOrLesson): void;

    public function remove(TeacherAbsence|TeacherAbsenceLesson $absenceOrLesson): void;

    /**
     * Removes all absences within the given timespan (start and end inclusive)
     *
     * @param DateTime $start
     * @param DateTime $end
     * @return int Number of removed absences
     */
    public function removeRange(DateTime $start, DateTime $end): int;
}