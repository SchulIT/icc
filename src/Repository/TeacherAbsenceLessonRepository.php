<?php

namespace App\Repository;

use App\Entity\TeacherAbsenceLesson;
use App\Entity\TimetableLesson;
use DateTime;

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

    public function findAllUnresolved(DateTime $startDate, DateTime $endDate): array {
        return $this->em->createQueryBuilder()
            ->select(['al', 'tu', 'a', 't'])
            ->from(TeacherAbsenceLesson::class, 'al')
            ->leftJoin('al.tuition', 'tu')
            ->leftJoin('al.absence', 'a')
            ->leftJoin('a.teacher', 't')
            ->where('al.lesson IS NULL')
            ->andWhere('al.date >= :start')
            ->andWhere('al.date <= :end')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->getQuery()->getResult();
    }

    public function persist(TeacherAbsenceLesson $absenceLesson): void {
        $this->em->persist($absenceLesson);
        $this->em->flush();
    }
}