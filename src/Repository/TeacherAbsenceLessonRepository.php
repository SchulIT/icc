<?php

namespace App\Repository;

use App\Entity\TeacherAbsenceLesson;
use App\Entity\TimetableLesson;

class TeacherAbsenceLessonRepository extends AbstractRepository implements TeacherAbsenceLessonRepositoryInterface {

    public function findOneForLesson(TimetableLesson $lesson): ?TeacherAbsenceLesson {
        return $this->em->createQueryBuilder()
            ->select('al')
            ->from(TeacherAbsenceLesson::class, 'al')
            ->leftJoin('al.lesson', 'l')
            ->where('l.id = :lesson')
            ->setParameter('lesson', $lesson->getId())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}