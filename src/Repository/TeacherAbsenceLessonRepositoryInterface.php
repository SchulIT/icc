<?php

namespace App\Repository;

use App\Entity\TeacherAbsenceLesson;
use App\Entity\TimetableLesson;

interface TeacherAbsenceLessonRepositoryInterface {
    public function findOneForLesson(TimetableLesson $lesson): ?TeacherAbsenceLesson;
}