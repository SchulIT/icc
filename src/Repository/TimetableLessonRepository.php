<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\Room;
use App\Entity\Student;
use App\Entity\Subject;
use App\Entity\Teacher;
use App\Entity\TimetableLesson;
use App\Entity\TimetablePeriod;
use Doctrine\ORM\QueryBuilder;

class TimetableLessonRepository extends AbstractTransactionalRepository implements TimetableLessonRepositoryInterface {

    private function getDefaultQueryBuilder(): QueryBuilder {
        return $this->em->createQueryBuilder()
            ->select(['l', 'p', 't', 'w'])
            ->from(TimetableLesson::class, 'l')
            ->leftJoin('l.period', 'p')
            ->leftJoin('l.tuition', 't')
            ->leftJoin('l.week', 'w');
    }

    /**
     * @inheritDoc
     */
    public function findOneById(int $id): ?TimetableLesson {
        return $this->getDefaultQueryBuilder()
            ->where('l.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @inheritDoc
     */
    public function findAll() {
        return $this->getDefaultQueryBuilder()
            ->getQuery()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    public function persist(TimetableLesson $lesson): void {
        $this->em->persist($lesson);
        $this->flushIfNotInTransaction();
    }

    /**
     * @inheritDoc
     */
    public function remove(TimetableLesson $lesson): void {
        $this->em->remove($lesson);
        $this->flushIfNotInTransaction();
    }

    /**
     * @inheritDoc
     */
    public function findAllByPeriodAndGrade(TimetablePeriod $period, Grade $grade) {
        $qb = $this->getDefaultQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('lInner')
            ->from(TimetableLesson::class, 'lInner')
            ->leftJoin('lInner.period', 'pInner')
            ->leftJoin('lInner.tuition', 'tInner')
            ->leftJoin('tInner.studyGroup', 'sgInner')
            ->leftJoin('sgInner.grades', 'gInner')
            ->where('pInner.id = :period')
            ->andWhere('gInner.id = :grade');

        $qb->setParameter('grade', $grade->getId())
            ->setParameter('period', $period->getId());

        $qb->where(
            $qb->expr()->in('l.id', $qbInner->getDQL())
        );

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByPeriodAndTeacher(TimetablePeriod $period, Teacher $teacher) {
        $qb = $this->getDefaultQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('lInner')
            ->from(TimetableLesson::class, 'lInner')
            ->leftJoin('lInner.period', 'pInner')
            ->leftJoin('lInner.tuition', 'tInner')
            ->leftJoin('tInner.teacher', 'ttInner')
            ->leftJoin('tInner.additionalTeachers', 'atInner')
            ->where('pInner.id = :period')
            ->andWhere(
                $qb->expr()->orX(
                    'ttInner.id = :teacher',
                    'atInner.id = :teacher'
                )
            );

        $qb->setParameter('teacher', $teacher->getId())
            ->setParameter('period', $period->getId());

        $qb->where(
            $qb->expr()->in('l.id', $qbInner->getDQL())
        );

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByPeriodAndRoom(TimetablePeriod $period, Room $room) {
        $qb = $this->getDefaultQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('lInner')
            ->from(TimetableLesson::class, 'lInner')
            ->leftJoin('lInner.period', 'pInner')
            ->where('pInner.id = :period')
            ->andWhere('lInner.room = :room');

        $qb->setParameter('room', $room->getName())
            ->setParameter('period', $period->getId());

        $qb->where(
            $qb->expr()->in('l.id', $qbInner->getDQL())
        );

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByPeriodAndStudent(TimetablePeriod $period, Student $student) {
        $qb = $this->getDefaultQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('lInner')
            ->from(TimetableLesson::class, 'lInner')
            ->leftJoin('lInner.period', 'pInner')
            ->leftJoin('lInner.tuition', 'tInner')
            ->leftJoin('tInner.studyGroup', 'sgInner')
            ->leftJoin('sgInner.memberships', 'sgmInner')
            ->leftJoin('sgmInner.student', 'studentInner')
            ->where('pInner.id = :period')
            ->andWhere('studentInner.id = :student');

        $qb->setParameter('student', $student->getId())
            ->setParameter('period', $period->getId());

        $qb->where(
            $qb->expr()->in('l.id', $qbInner->getDQL())
        );

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByPeriodAndSubjects(TimetablePeriod $period, array $subjects) {
        $qb = $this->getDefaultQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('lInner')
            ->from(TimetableLesson::class, 'lInner')
            ->leftJoin('lInner.period', 'pInner')
            ->leftJoin('lInner.tuition', 'tInner')
            ->leftJoin('tInner.subject', 'sInner')
            ->where('pInner.id = :period')
            ->andWhere(
                $qb->expr()->in('sInner.id', ':subjects')
            );

        $subjectIds = array_map(function(Subject $subject) {
            return $subject->getId();
        }, $subjects);

        $qb->setParameter('subjects', $subjectIds)
            ->setParameter('period', $period->getId());

        $qb->where(
            $qb->expr()->in('l.id', $qbInner->getDQL())
        );

        return $qb->getQuery()->getResult();
    }
}