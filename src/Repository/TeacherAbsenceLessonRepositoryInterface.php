<?php

namespace App\Repository;

use App\Entity\TeacherAbsenceLesson;
use App\Entity\TimetableLesson;
use DateTime;

interface TeacherAbsenceLessonRepositoryInterface {
    public function findOneForLesson(TimetableLesson $lesson): ?TeacherAbsenceLesson;

    /**
     * @return TeacherAbsenceLesson[]
     */
    public function findAllUnresolved(DateTime $startDate, DateTime $endDate): array;

    public function persist(TeacherAbsenceLesson $absenceLesson): void;
}