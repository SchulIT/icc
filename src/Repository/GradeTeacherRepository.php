<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\GradeTeacher;
use App\Entity\Section;

class GradeTeacherRepository extends AbstractTransactionalRepository implements GradeTeacherRepositoryInterface {

    /**
     * @inheritDoc
     */
    public function findAll() {
        return $this->em->getRepository(GradeTeacher::class)
            ->findAll();
    }

    public function persist(GradeTeacher $gradeTeacher): void {
        $this->em->persist($gradeTeacher);
        $this->flushIfNotInTransaction();
    }

    public function removeAll(Section $section): void {
        $qb = $this->em->createQueryBuilder()
            ->delete()
            ->from(GradeTeacher::class, 'g');

        $qbInner = $this->em->createQueryBuilder()
            ->select('gInner.id')
            ->from(GradeTeacher::class, 'gInner')
            ->leftJoin('gInner.section', 'sInner')
            ->where('sInner.id = :section');

        $qb->where(
            $qb->expr()->in('g.id', $qbInner->getDQL())
        )
            ->setParameter('section', $section->getId());

        $qb->getQuery()->execute();
    }



}