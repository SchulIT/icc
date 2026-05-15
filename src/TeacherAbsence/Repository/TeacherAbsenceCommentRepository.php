<?php

namespace App\TeacherAbsence\Repository;

use App\Framework\Repository\AbstractRepository;
use App\TeacherAbsence\Repository\TeacherAbsenceCommentRepositoryInterface;
use App\TeacherAbsence\Entity\TeacherAbsenceComment;
use App\Timetable\Entity\TimetableLesson;
use DateTime;

class TeacherAbsenceCommentRepository extends AbstractRepository implements TeacherAbsenceCommentRepositoryInterface {

    public function persist(TeacherAbsenceComment $comment): void {
        $this->em->persist($comment);
        $this->em->flush();
    }

    public function remove(TeacherAbsenceComment $comment): void {
        $this->em->remove($comment);
        $this->em->flush();
    }
}