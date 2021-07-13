<?php

namespace App\Repository;

use App\Entity\LessonAttendance;
use App\Entity\LessonAttendanceType;
use App\Entity\LessonEntry;
use App\Entity\Student;
use DateTime;

class LessonAttendanceRepository extends AbstractRepository implements LessonAttendanceRepositoryInterface {

    public function findAbsentByStudents(array $students, DateTime $dateTime): array {
        $studentIds = array_map(function(Student $student) {
            return $student->getId();
        }, $students);

        $qb = $this->em->createQueryBuilder();

        $qb->select(['a', 's', 'e'])
            ->from(LessonAttendance::class, 'a')
            ->leftJoin('a.student', 's')
            ->leftJoin('a.entry', 'e')
            ->where($qb->expr()->in('s.id', ':students'))
            ->andWhere('e.date = :date')
            ->andWhere('a.type = :type')
            ->setParameter('students', $studentIds)
            ->setParameter('date', $dateTime)
            ->setParameter('type', LessonAttendanceType::Absent);

        return $qb->getQuery()->getResult();
    }

    public function persist(LessonAttendance $attendance): void {
        $this->em->persist($attendance);
        $this->em->flush();
    }

    public function remove(LessonAttendance $attendance): void {
        $this->em->remove($attendance);
        $this->em->flush();
    }


    private function countAttendance(LessonEntry $entry, int $type): int {
        return $this->em
            ->createQueryBuilder()
            ->select('COUNT(a.id)')
            ->from(LessonAttendance::class, 'a')
            ->leftJoin('a.entry', 'e')
            ->where('e.id = :entry')
            ->andWhere('a.type = :type')
            ->setParameter('entry', $entry->getId())
            ->setParameter('type', $type)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countAbsent(LessonEntry $entry): int {
        return $this->countAttendance($entry, LessonAttendanceType::Absent);
    }

    public function countPresent(LessonEntry $entry): int {
        return $this->countAttendance($entry, LessonAttendanceType::Present);
    }

    public function countLate(LessonEntry $entry): int {
        return $this->countAttendance($entry, LessonAttendanceType::Late);
    }
}