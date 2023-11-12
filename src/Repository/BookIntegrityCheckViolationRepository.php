<?php

namespace App\Repository;

use App\Entity\BookIntegrityCheckViolation;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\Teacher;
use DateTime;

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

    public function findAllByTeacher(Teacher $teacher, DateTime $start, DateTime $end): array {
        return [ ];
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