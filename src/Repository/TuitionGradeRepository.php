<?php

namespace App\Repository;

use App\Entity\Section;
use App\Entity\Student;
use App\Entity\Tuition;
use App\Entity\TuitionGrade;
use Doctrine\ORM\QueryBuilder;

class TuitionGradeRepository extends AbstractTransactionalRepository implements TuitionGradeRepositoryInterface {

    private function createDefaultQueryBuilder(): QueryBuilder {
        return $this->em->createQueryBuilder()
            ->select(['g', 't', 's', 'c'])
            ->from(TuitionGrade::class, 'g')
            ->leftJoin('g.category', 'c')
            ->leftJoin('g.student', 's')
            ->leftJoin('g.tuition', 't');
    }

    public function findAllByTuition(Tuition $tuition): array {
        return $this->createDefaultQueryBuilder()
            ->where('t.id = :tuition')
            ->setParameter('tuition', $tuition->getId())
            ->getQuery()
            ->getResult();
    }

    public function findAllByStudent(Student $student, Section $section): array {
        return $this->createDefaultQueryBuilder()
            ->leftJoin('t.section', 'sec')
            ->where('s.id = :student')
            ->andWhere('sec.id = :section')
            ->setParameter('student', $student->getId())
            ->setParameter('section', $section->getId())
            ->getQuery()
            ->getResult();
    }

    public function persist(TuitionGrade $grade): void {
        $this->em->persist($grade);
        $this->flushIfNotInTransaction();
    }
}