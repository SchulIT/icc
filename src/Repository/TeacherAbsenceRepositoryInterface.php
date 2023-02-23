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
}