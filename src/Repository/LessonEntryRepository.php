<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\LessonEntry;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\Tuition;
use DateTime;
use Doctrine\ORM\QueryBuilder;

class LessonEntryRepository extends AbstractRepository implements LessonEntryRepositoryInterface {

    private function createDefaultQueryBuilder(): QueryBuilder {
        return $this->em
            ->createQueryBuilder()
            ->select(['e', 't', 'tt', 'l'])
            ->from(LessonEntry::class, 'e')
            ->leftJoin('e.teacher', 't')
            ->leftJoin('e.tuition', 'tt')
            ->leftJoin('e.lesson', 'l')
            ->andWhere('tt.isBookEnabled = true');
    }

    private function applyStartEnd(QueryBuilder $qb, DateTime $start, DateTime $end): QueryBuilder {
        return $qb
            ->andWhere('l.date >= :start')
            ->andWhere('l.date <= :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end);
    }

    /**
     * @inheritDoc
     */
    public function findAllByTuition(Tuition $tuition, DateTime $start, DateTime $end): array {
        if($tuition->isBookEnabled() === false) {
            return [ ];
        }

        $qb = $this->createDefaultQueryBuilder();
        $qb = $this->applyStartEnd($qb, $start, $end);

        $qb->andWhere('tt.id = :tuition')
            ->setParameter('tuition', $tuition->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllBySubstituteTeacher(Teacher $teacher, DateTime $start, DateTime $end): array {
        $qb = $this->createDefaultQueryBuilder();
        $qb = $this->applyStartEnd($qb, $start, $end);

        $qb->andWhere('e.replacementTeacher = :teacher')
            ->setParameter('teacher', $teacher->getId());

        return $qb->getQuery()->getResult();
    }

    private function applyGrade($qb, Grade $grade): QueryBuilder {
        $qbInner = $this->em->createQueryBuilder()
            ->select('tInner.id')
            ->from(Tuition::class, 'tInner')
            ->leftJoin('tInner.studyGroup', 'sInner')
            ->leftJoin('sInner.grades', 'gInner')
            ->where('gInner.id = :grade');

        $qb->andWhere(
            $qb->expr()->in('tt.id', $qbInner->getDQL())
        )
            ->setParameter('grade', $grade->getId());

        return $qb;
    }

    /**
     * @inheritDoc
     */
    public function findAllByGrade(Grade $grade, DateTime $start, DateTime $end): array {
        $qb = $this->createDefaultQueryBuilder();
        $qb = $this->applyStartEnd($qb, $start, $end);
        $qb = $this->applyGrade($qb, $grade);

        return $qb->getQuery()->getResult();
    }

    public function findAllByGradeWithExercises(Grade $grade, DateTime $start, DateTime $end): array {
        $qb = $this->createDefaultQueryBuilder();
        $qb = $this->applyStartEnd($qb, $start, $end);
        $qb = $this->applyGrade($qb, $grade);

        $qb->andWhere('e.exercises IS NOT NULL')
            ->orderBy('l.date', 'desc')
            ->addOrderBy('l.lessonStart', 'asc');

        return $qb->getQuery()->getResult();
    }

    public function findAllByStudentWithExercises(Student $student, DateTime $start, DateTime $end): array {
        $qb = $this->createDefaultQueryBuilder();
        $qb = $this->applyStartEnd($qb, $start, $end);

        $qbInner = $this->em->createQueryBuilder()
            ->select('tInner.id')
            ->from(Tuition::class, 'tInner')
            ->leftJoin('tInner.studyGroup', 'sgInner')
            ->leftJoin('sgInner.memberships', 'sgmInner')
            ->where('sgmInner.student = :student');

        $qb->andWhere(
            $qb->expr()->in('tt.id', $qbInner->getDQL())
        )
            ->setParameter('student', $student->getId());

        $qb->andWhere('e.exercises IS NOT NULL')
            ->orderBy('l.date', 'desc')
            ->addOrderBy('l.lessonStart', 'asc');

        return $qb->getQuery()->getResult();
    }

    public function persist(LessonEntry $entry): void {
        $this->em->persist($entry);
        $this->em->flush();
    }

    public function remove(LessonEntry $entry): void {
        $this->em->remove($entry);
        $this->em->flush();
    }
}