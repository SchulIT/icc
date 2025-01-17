<?php

namespace App\Repository;

use App\Entity\TeacherAbsenceComment;
use App\Entity\TimetableLesson;
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