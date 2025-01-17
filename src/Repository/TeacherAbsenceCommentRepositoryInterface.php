<?php

namespace App\Repository;

use App\Entity\TeacherAbsenceComment;
use App\Entity\TimetableLesson;
use DateTime;

interface TeacherAbsenceCommentRepositoryInterface {
    public function persist(TeacherAbsenceComment $comment): void;

    public function remove(TeacherAbsenceComment $comment): void;
}