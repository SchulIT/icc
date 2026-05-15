<?php

namespace App\TeacherAbsence\Repository;

use App\TeacherAbsence\Entity\TeacherAbsenceComment;
use App\Timetable\Entity\TimetableLesson;
use DateTime;

interface TeacherAbsenceCommentRepositoryInterface {
    public function persist(TeacherAbsenceComment $comment): void;

    public function remove(TeacherAbsenceComment $comment): void;
}