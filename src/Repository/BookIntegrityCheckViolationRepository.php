<?php

namespace App\Repository;

use App\Entity\BookIntegrityCheckViolation;
use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\Teacher;
use DateTime;
use Doctrine\ORM\QueryBuilder;

class BookIntegrityCheckViolationRepository extends AbstractTransactionalRepository implements BookIntegrityCheckViolationRepositoryInterface {

    public function findAllByStudent(Student $student, DateTime $start, DateTime $end): array {
        return $this->em->createQueryBuilder()
            ->select(['v', 's', 'l'])
            ->from(BookIntegrityCheckViolation::class, 'v')
            ->leftJoin('v.student', 's')
            ->leftJoin('v.lesson', 'l')
            ->where('s.id = :student')
            ->andWhere('v.date >= :start')
            ->andWhere('v.date <= :end')
            ->setParameter('student', $student->getId())
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
    }

    public function countAllByStudents(array $students, DateTime $start, DateTime $end): int {
        $studentIds = array_map(fn(Student $student) => $student->getId(), $students);

        return $this->em->createQueryBuilder()
            ->select('COUNT(DISTINCT v.id)')
            ->from(BookIntegrityCheckViolation::class, 'v')
            ->leftJoin('v.student', 's')
            ->leftJoin('v.lesson', 'l')
            ->where('s.id IN(:students)')
            ->andWhere('v.date >= :start')
            ->andWhere('v.date <= :end')
            ->andWhere('v.isSuppressed = false')
            ->setParameter('students', $studentIds)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function getByTeacherQueryBuilder(Teacher $teacher, DateTime $start, DateTime $end): QueryBuilder {
        return $this->em->createQueryBuilder()
            ->select(['v', 's', 'l'])
            ->from(BookIntegrityCheckViolation::class, 'v')
            ->leftJoin('v.student', 's')
            ->leftJoin('v.lesson', 'l')
            ->leftJoin('l.teachers', 't')
            ->where('t.id = :teacher')
            ->andWhere('v.date >= :start')
            ->andWhere('v.date <= :end')
            ->setParameter('teacher', $teacher->getId())
            ->setParameter('start', $start)
            ->setParameter('end', $end);
    }

    public function findAllByTeacher(Teacher $teacher, DateTime $start, DateTime $end): array {
        return $this->getByTeacherQueryBuilder($teacher, $start, $end)
            ->getQuery()
            ->getResult();
    }

    public function countAllByTeacher(Teacher $teacher, DateTime $start, DateTime $end): int {
        return $this->getByTeacherQueryBuilder($teacher, $start, $end)
            ->select('COUNT(DISTINCT v.id)')
            ->andWhere('v.isSuppressed = false')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function persist(BookIntegrityCheckViolation $violation): void {
        $this->em->persist($violation);
        $this->flushIfNotInTransaction();
    }

    public function remove(BookIntegrityCheckViolation $violation): void {
        $this->em->remove($violation);
        $this->flushIfNotInTransaction();
    }


}