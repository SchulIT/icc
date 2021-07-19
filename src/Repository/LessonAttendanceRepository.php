<?php

namespace App\Repository;

use App\Entity\Attendance;
use App\Entity\AttendanceType;
use App\Entity\LessonAttendance;
use App\Entity\LessonAttendanceType;
use App\Entity\LessonEntry;
use App\Entity\Student;
use App\Entity\Tuition;
use DateTime;
use Doctrine\ORM\QueryBuilder;

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
            ->leftJoin('e.lesson', 'l')
            ->where($qb->expr()->in('s.id', ':students'))
            ->andWhere('l.date = :date')
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

    private function getDefaultQueryBuilder(): QueryBuilder {
        return $this->em->createQueryBuilder()
            ->select(['a'])
            ->from(LessonAttendance::class, 'a')
            ->leftJoin('a.entry', 'e')
            ->leftJoin('a.student', 's');
    }

    private function applyTuition(QueryBuilder $queryBuilder, array $tuitions): QueryBuilder {
        if(count($tuitions) === 0) {
            return $queryBuilder;
        }

        $qbInner = $this->em->createQueryBuilder()
            ->select(['aInner.id'])
            ->from(LessonAttendance::class, 'aInner')
            ->leftJoin('aInner.entry', 'eInner')
            ->leftJoin('eInner.tuition', 'tInner')
            ->where('tInner.id IN(:tuitions)');

        $ids = array_map(function(Tuition $tuition) {
            return $tuition->getId();
        }, $tuitions);

        $queryBuilder
            ->andWhere(
                $queryBuilder->expr()->in(
                    'a.id', $qbInner->getDQL()
                )
            )
            ->setParameter('tuitions', $ids);

        return $queryBuilder;
    }

    public function findLateByStudent(Student $student, array $tuitions): array {
        $qb = $this->getDefaultQueryBuilder()
            ->where('s.id = :student')
            ->andWhere('a.type = :type')
            ->setParameter('student', $student)
            ->setParameter('type', LessonAttendanceType::Late);
        $this->applyTuition($qb, $tuitions);

        return $qb->getQuery()->getResult();
    }

    public function findAbsentByStudent(Student $student, array $tuitions): array {
        $qb = $this->getDefaultQueryBuilder()
            ->where('s.id = :student')
            ->andWhere('a.type = :type')
            ->setParameter('student', $student)
            ->setParameter('type', LessonAttendanceType::Absent);
        $this->applyTuition($qb, $tuitions);

        return $qb->getQuery()->getResult();
    }
}