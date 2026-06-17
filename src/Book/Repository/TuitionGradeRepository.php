<?php

namespace App\Book\Repository;

use App\Book\Entity\TuitionGrade;
use App\Book\Entity\TuitionGradeCategory;
use App\Common\Entity\Section;
use App\Common\Entity\Student;
use App\Common\Entity\Tuition;
use App\Framework\Repository\AbstractTransactionalRepository;
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

    public function countByTuitionGradeCategory(TuitionGradeCategory $category): int {
        return $this->em->createQueryBuilder()
            ->select('COUNT(DISTINCT g.id)')
            ->from(TuitionGrade::class, 'g')
            ->leftJoin('g.category', 'c')
            ->where('c.id = :category')
            ->setParameter('category', $category->getId())
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function persist(TuitionGrade $grade): void {
        $this->em->persist($grade);
        $this->flushIfNotInTransaction();
    }

    public function removeForSection(Section $section): int {
        $qb = $this->em->createQueryBuilder()
            ->delete(TuitionGrade::class, 'g');

        $qbInner = $this->em->createQueryBuilder()
            ->select('tInner.id')
            ->from(Tuition::class, 'tInner')
            ->where('tInner.section = :section');

        $qb->where($qb->expr()->in('g.tuition', $qbInner->getDQL()))
            ->setParameter('section', $section->getId());

        return $qb->getQuery()->execute();
    }
}