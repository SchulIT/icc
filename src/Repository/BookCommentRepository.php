<?php

namespace App\Repository;

use App\Entity\BookComment;
use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\Tuition;
use DateTime;
use function Doctrine\ORM\QueryBuilder;

class BookCommentRepository extends AbstractRepository implements BookCommentRepositoryInterface {

    public function findAllByDateAndGrade(Grade $grade, Section $section, DateTime $start, DateTime $end): array {
        $qbStudents = $this->em->createQueryBuilder()
            ->select('student.id')
            ->from(Grade::class, 'gInner')
            ->leftJoin('gInner.memberships', 'mInner')
            ->leftJoin('mInner.student', 'student')
            ->leftJoin('mInner.section', 'secInner')
            ->where('secInner.id = :section')
            ->andWhere('gInner.id = :grade');

        $qbInner = $this->em->createQueryBuilder()
            ->select('cInner.id')
            ->from(BookComment::class, 'cInner')
            ->leftJoin('cInner.students', 'sInner');

        $qbInner->where(
            $qbInner->expr()->in('sInner.id', $qbStudents->getDQL())
        );

        $qb = $this->em->createQueryBuilder()
            ->select(['c', 's'])
            ->from(BookComment::class, 'c')
            ->leftJoin('c.students', 's');
        $qb->where(
            $qb->expr()->in('c.id', $qbInner->getDQL())
        )
            ->andWhere('c.date >= :start')
            ->andWhere('c.date <= :end')
            ->setParameter('start', $start)
            ->setParameter('end', $start)
            ->setParameter('section', $section->getId())
            ->setParameter('grade', $grade->getId());

        return $qb->getQuery()->getResult();
    }

    public function findAllByDateAndTuition(Tuition $tuition, DateTime $start, DateTime $end): array {
        $qbStudents = $this->em->createQueryBuilder()
            ->select('student.id')
            ->from(Tuition::class, 'tInner')
            ->leftJoin('tInner.studyGroup', 'sgInner')
            ->leftJoin('sgInner.memberships', 'mInner')
            ->leftJoin('mInner.student', 'student')
            ->where('tInner.id = :tuition');

        $qbInner = $this->em->createQueryBuilder()
            ->select('cInner.id')
            ->from(BookComment::class, 'cInner')
            ->leftJoin('cInner.students', 'sInner');

        $qbInner->where(
            $qbInner->expr()->in('sInner.id', $qbStudents->getDQL())
        );

        $qb = $this->em->createQueryBuilder()
            ->select(['c', 's'])
            ->from(BookComment::class, 'c')
            ->leftJoin('c.students', 's');
        $qb->where(
            $qb->expr()->in('c.id', $qbInner->getDQL())
        )
            ->andWhere('c.date >= :start')
            ->andWhere('c.date <= :end')
            ->setParameter('start', $start)
            ->setParameter('end', $start)
            ->setParameter('tuition', $tuition->getId());

        return $qb->getQuery()->getResult();
    }

    public function persist(BookComment $comment): void {
        $this->em->persist($comment);
        $this->em->flush();
    }

    public function remove(BookComment $comment): void {
        $this->em->remove($comment);
        $this->em->flush();
    }
}