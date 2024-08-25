<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\Room;
use App\Entity\Student;
use App\Entity\Subject;
use App\Entity\Teacher;
use App\Entity\TimetableLesson;
use App\Entity\Tuition;
use DateTime;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class TimetableLessonRepository extends AbstractTransactionalRepository implements TimetableLessonRepositoryInterface {

    private function getDefaultQueryBuilder(?DateTime $start = null, ?DateTime $end = null): QueryBuilder {
        $qb = $this->em->createQueryBuilder()
            ->select(['l', 'r', 't', 'sg', 'g']) // do not hydrate the tuition as it may be null and get hydrated (https://github.com/doctrine/orm/issues/8446)
            ->from(TimetableLesson::class, 'l')
            ->leftJoin('l.tuition', 't')
            ->leftJoin('t.studyGroup', 'sg')
            ->leftJoin('sg.grades', 'g')
            ->leftJoin('l.room', 'r')
            ->leftJoin('l.subject', 's');

        if($start !== null) {
            $qb->andWhere('l.date >= :start')
                ->setParameter('start', $start);
        }

        if($end !== null) {
            $qb->andWhere('l.date <= :end')
                ->setParameter('end', $end);
        }

        return $qb;
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

    public function findOneByUuid(string $uuid): ?TimetableLesson {
        return $this->getDefaultQueryBuilder()
            ->where('l.uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->getQuery()
            ->getOneOrNullResult();
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

    public function removeRange(DateTime $start, DateTime $end): void {
        $this->em->createQueryBuilder()
            ->delete(TimetableLesson::class, 'l')
            ->where('l.date >= :start')
            ->andWhere('l.date <= :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->execute();
        $this->flushIfNotInTransaction();
    }

    /**
     * @inheritDoc
     */
    public function findAllByGrade(DateTime $start, DateTime $end, Grade $grade): array {
        $qb = $this->getDefaultQueryBuilder($start, $end);

        $qbInner = $this->em->createQueryBuilder()
            ->select('lInner')
            ->from(TimetableLesson::class, 'lInner')
            ->leftJoin('lInner.grades', 'gInner')
            ->where('gInner.id = :grade');

        $qb->setParameter('grade', $grade->getId());

        $qb->andWhere(
            $qb->expr()->in('l.id', $qbInner->getDQL())
        );

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByTeacher(DateTime $start, DateTime $end, Teacher $teacher): array {
        $qb = $this->getDefaultQueryBuilder($start, $end);

        $qbInner = $this->em->createQueryBuilder()
            ->select('tInner.id')
            ->from(TimetableLesson::class, 'tInner')
            ->leftJoin('tInner.teachers', 'teacherInner')
            ->where('teacherInner.id = :teacher');

        $qb->andWhere(
            $qb->expr()->in('l.id', $qbInner->getDQL())
        )
            ->setParameter('teacher', $teacher->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByRoom(DateTime $start, DateTime $end, Room $room): array {
        $qb = $this->getDefaultQueryBuilder($start, $end);

        $qbInner = $this->em->createQueryBuilder()
            ->select('lInner.id')
            ->from(TimetableLesson::class, 'lInner')
            ->leftJoin('lInner.room', 'rInner')
            ->where('rInner.id = :room')
            ->setParameter('room', $room->getId());

        $qb->andWhere(
            $qb->expr()->in('l.id', $qbInner->getDQL())
        );

        $qb->setParameter('room', $room->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByStudent(DateTime $start, DateTime $end, Student $student): array {
        $qb = $this->getDefaultQueryBuilder($start, $end);

        $qbInner = $this->em->createQueryBuilder()
            ->select('lInner')
            ->from(TimetableLesson::class, 'lInner')
            ->leftJoin('lInner.tuition', 'tInner')
            ->leftJoin('tInner.studyGroup', 'sgInner')
            ->leftJoin('sgInner.memberships', 'sgmInner')
            ->leftJoin('sgmInner.student', 'studentInner')
            ->where('studentInner.id = :student');

        $qb->setParameter('student', $student->getId());

        $qb->andWhere(
            $qb->expr()->in('l.id', $qbInner->getDQL())
        );

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllBySubjects(DateTime $start, DateTime $end, array $subjects): array {
        $qb = $this->getDefaultQueryBuilder($start, $end);

        $qbInner = $this->em->createQueryBuilder()
            ->select('lInner')
            ->from(TimetableLesson::class, 'lInner')
            ->leftJoin('lInner.tuition', 'tInner')
            ->leftJoin('tInner.subject', 'sInner')
            ->leftJoin('lInner.subject', 'lsInner')
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('sInner.id', ':subjects'),
                    $qb->expr()->in('lsInner.id', ':subjects')
                )
            );

        $subjectIds = array_map(fn(Subject $subject) => $subject->getId(), $subjects);

        $qb->setParameter('subjects', $subjectIds);

        $qb->andWhere(
            $qb->expr()->in('l.id', $qbInner->getDQL())
        );

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByTuitions(DateTime $start, DateTime $end, array $tuitions): array {
        $ids = array_map(fn(Tuition $tuition) => $tuition->getId(), $tuitions);

        $qb = $this->getDefaultQueryBuilder($start, $end);

        $qbInner = $this->em->createQueryBuilder()
            ->select('lInner')
            ->from(TimetableLesson::class, 'lInner')
            ->leftJoin('lInner.tuition', 'tInner')
            ->where($qb->expr()->in('tInner.id', ':tuitions'));

        $qb->setParameter('tuitions', $ids);

        $qb->andWhere(
            $qb->expr()->in('l.id', $qbInner->getDQL())
        );

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findOneByDateAndRoomAndLesson(DateTime $date, Room $room, int $lessonNumber): ?TimetableLesson {
        $qb = $this->getDefaultQueryBuilder($date, $date);

        $qbInner = $this->em->createQueryBuilder()
            ->select('lInner.id')
            ->from(TimetableLesson::class, 'lInner')
            ->leftJoin('lInner.room', 'rInner')
            ->where('lInner.lessonStart <= :lesson')
            ->andWhere('lInner.lessonEnd >= :lesson')
            ->andWhere('rInner.id = :room');
        $qb
            ->setParameter('room', $room->getId())
            ->setParameter('lesson', $lessonNumber);

        $qb->andWhere(
            $qb->expr()->in('l.id', $qbInner->getDQL())
        )
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByRange(DateTime $start, DateTime $end): array {
        return $this->getDefaultQueryBuilder($start, $end)
            ->getQuery()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array {
        return $this->em->getRepository(TimetableLesson::class)
            ->findAll();
    }

    /**
     * @inheritDoc
     */
    public function removeStartingFrom(DateTime $dateTime): int {
        return $this->em->createQueryBuilder()
            ->delete(TimetableLesson::class, 'l')
            ->where('l.date >= :date')
            ->setParameter('date', $dateTime)
            ->getQuery()
            ->execute();
    }

    private function getMissingQueryBuilder(): QueryBuilder {
        $qbInner = $this->em->createQueryBuilder()
            ->select('lIntern.id')
            ->from(TimetableLesson::class, 'lIntern')
            ->leftJoin('lIntern.entries', 'eIntern')
            ->leftJoin('lIntern.tuition', 'tIntern')
            ->where('eIntern.id IS NOT NULL')
            ->andWhere('tIntern.isBookEnabled = true')
            ->groupBy('lIntern.id, lIntern.lessonEnd, lIntern.lessonStart')
            ->having('SUM(eIntern.lessonEnd - eIntern.lessonStart + 1) != (lIntern.lessonEnd - lIntern.lessonStart + 1)');

        $qb = $this->em->createQueryBuilder();

        $qb->select(['l', 'e', 't'])
            ->from(TimetableLesson::class, 'l')
            ->leftJoin('l.entries', 'e')
            ->leftJoin('l.tuition', 't')
            ->where('t.isBookEnabled = true')
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->isNull('e.id'),                    // lessons without any entries
                    $qb->expr()->in('l.id', $qbInner->getDQL())     // partly incomplete lessons
                )
            );

        return $qb;
    }

    private function getMissingByTeacherQueryBuilder(Teacher $teacher, DateTime $start, DateTime $end): QueryBuilder {
        $qb = $this->getMissingQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('lInner.id')
            ->from(TimetableLesson::class, 'lInner')
            ->where('lInner.date >= :start')
            ->andWhere('lInner.date <= :end')
            ->leftJoin('lInner.tuition', 'tInner')
            ->leftJoin('tInner.teachers', 'ttInner');
        $qbInner
            ->andWhere(
                'ttInner.id = :teacher'
            );

        $qb->andWhere(
            $qb->expr()->in('l.id', $qbInner->getDQL())
        )
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('teacher', $teacher->getId());

        return $qb;
    }

    public function getMissingByTeacherPaginator(int $itemsPerPage, int &$page, Teacher $teacher, DateTime $start, DateTime $end): Paginator {
        if($page < 1) {
            $page = 1;
        }

        $offset = ($page - 1) * $itemsPerPage;

        $paginator = new Paginator($this->getMissingByTeacherQueryBuilder($teacher, $start, $end)->orderBy('l.date', 'desc'));
        $paginator->getQuery()
            ->setMaxResults($itemsPerPage)
            ->setFirstResult($offset);

        return $paginator;
    }

    private function getMissingByGradeQueryBuilder(Grade $grade, DateTime $start, DateTime $end): QueryBuilder {
        $qb = $this->getMissingQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('lInner.id')
            ->from(TimetableLesson::class, 'lInner')
            ->where('lInner.date >= :start')
            ->andWhere('lInner.date <= :end')
            ->leftJoin('lInner.tuition', 'tInner')
            ->leftJoin('tInner.studyGroup', 'sgInner')
            ->leftJoin('sgInner.grades', 'gInner')
            ->andWhere('gInner.id = :grade');

        $qb->andWhere(
            $qb->expr()->in('l.id', $qbInner->getDQL())
        )
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('grade', $grade->getId());

        return $qb;
    }

    public function getMissingByGradePaginator(int $itemsPerPage, int &$page, Grade $grade, DateTime $start, DateTime $end): Paginator {
        if($page < 1) {
            $page = 1;
        }

        $offset = ($page - 1) * $itemsPerPage;

        $paginator = new Paginator($this->getMissingByGradeQueryBuilder($grade, $start, $end)->orderBy('l.date', 'desc'));
        $paginator->getQuery()
            ->setMaxResults($itemsPerPage)
            ->setFirstResult($offset);

        return $paginator;
    }

    private function getMissingByTuitionQueryBuilder(Tuition $tuition, DateTime $start, DateTime $end): QueryBuilder {
        $qb = $this->getMissingQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('lInner.id')
            ->from(TimetableLesson::class, 'lInner')
            ->where('lInner.date >= :start')
            ->andWhere('lInner.date <= :end')
            ->leftJoin('lInner.tuition', 'tInner')
            ->andWhere('tInner.id = :tuition');

        $qb->andWhere(
            $qb->expr()->in('l.id', $qbInner->getDQL())
        )
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('tuition', $tuition->getId());

        return $qb;
    }

    public function getMissingByTuitionPaginator(int $itemsPerPage, int &$page, Tuition $tuition, DateTime $start, DateTime $end): Paginator {
        if($page < 1) {
            $page = 1;
        }

        $offset = ($page - 1) * $itemsPerPage;

        $paginator = new Paginator($this->getMissingByTuitionQueryBuilder($tuition, $start, $end)->orderBy('l.date', 'desc'));
        $paginator->getQuery()
            ->setMaxResults($itemsPerPage)
            ->setFirstResult($offset);

        return $paginator;
    }

    public function countMissingByGrade(Grade $grade, DateTime $start, DateTime $end): int {
        return $this->getMissingByGradeQueryBuilder($grade, $start, $end)
            ->select('COUNT(DISTINCT l.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countMissingByTeacher(Teacher $teacher, DateTime $start, DateTime $end): int {
        return $this->getMissingByTeacherQueryBuilder($teacher, $start, $end)
            ->select('COUNT(DISTINCT l.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countMissingByTuition(Tuition $tuition, DateTime $start, DateTime $end): int {
        return $this->getMissingByTuitionQueryBuilder($tuition, $start, $end)
            ->select('COUNT(DISTINCT l.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countHoldLessons(array $tuitions, ?Student $student): int {
        $tuitionIds = array_map(fn(Tuition $tuition) => $tuition->getId(), $tuitions);

        $qb = $this->em->createQueryBuilder()
            ->select('SUM(l.lessonEnd - l.lessonStart + 1)')
            ->from(TimetableLesson::class, 'l');

        $qbInner = $this->em->createQueryBuilder()
            ->select('lInner.id')
            ->from(TimetableLesson::class, 'lInner')
            ->leftJoin('lInner.tuition', 'tInner')
            ->leftJoin('lInner.entries', 'eInner')
            ->where('tInner.id IN (:tuitions)');

        if($student !== null) {
            $qbInner->leftJoin('tInner.studyGroup', 'sgInner')
                ->leftJoin('sgInner.memberships', 'mInner')
                ->leftJoin('mInner.student', 'sInner')
                ->leftJoin('eInner.attendances', 'aInner')
                ->andWhere('aInner.student = :student')
                ->andWhere('sInner.id = :student');

            $qb->setParameter('student', $student->getId());
        }

        $qb
            ->where($qbInner->expr()->in('l.id', $qbInner->getDQL()))
            ->setParameter('tuitions', $tuitionIds);

        return (int)$qb->getQuery()
            ->getSingleScalarResult();
    }

    public function findAllByDate(DateTime $dateTime): array {
        return $this->getDefaultQueryBuilder($dateTime, $dateTime)
            ->getQuery()
            ->getResult();
    }

    public function findOneByDateAndTeacher(DateTime $date, int $lessonStart, int $lessonEnd, Teacher $teacher): ?TimetableLesson {
        $qbInner = $this->em->createQueryBuilder()
            ->select('tInner.id')
            ->from(TimetableLesson::class, 'tInner')
            ->leftJoin('tInner.teachers', 'teacherInner')
            ->where('teacherInner.id = :teacher');

        $qb = $this->getDefaultQueryBuilder($date, $date)
            ->andWhere('l.lessonStart = :lessonStart')
            ->andWhere('l.lessonEnd = :lessonEnd');

        return $qb
            ->andWhere(
                $qb->expr()->in('l.id', $qbInner->getDQL())
            )
            ->setParameter('lessonStart', $lessonStart)
            ->setParameter('lessonEnd', $lessonEnd)
            ->setParameter('teacher', $teacher->getId())
            ->getQuery()
            ->getOneOrNullResult();
    }

}