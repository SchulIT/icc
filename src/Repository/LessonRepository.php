<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\Lesson;
use App\Entity\Teacher;
use App\Entity\Tuition;
use DateTime;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class LessonRepository extends AbstractTransactionalRepository implements LessonRepositoryInterface {

    public function countByDate(DateTime $start, DateTime $end): int {
        return $this->em
            ->createQueryBuilder()
            ->select('COUNT(l.id)')
            ->from(Lesson::class, 'l')
            ->where('l.date >= :start')
            ->andWhere('l.date <= :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByDate(DateTime $dateTime): array {
        return $this->em->getRepository(Lesson::class)
            ->findBy([
                'date' => $dateTime
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findAllByTuitions(array $tuitions, DateTime $start, DateTime $end): array {
        $ids = array_map(function(Tuition $tuition) {
            return $tuition->getId();
        }, $tuitions);

        $qb = $this->em->createQueryBuilder();

        $qb->select(['l'])
            ->from(Lesson::class, 'l')
            ->leftJoin('l.tuition', 't')
            ->where(
                $qb->expr()->in('t.id', ':tuitions')
            )
            ->andWhere('l.date >= :start')
            ->andWhere('l.date <= :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('tuitions', $ids);

        return $qb->getQuery()->getResult();
    }

    public function persist(Lesson $lesson): void {
        $this->em->persist($lesson);
        $this->flushIfNotInTransaction();
    }

    public function remove(Lesson $lesson): void {
        $this->em->remove($lesson);
        $this->flushIfNotInTransaction();
    }

    public function countMissingByTeacher(Teacher $teacher, DateTime $start, DateTime $end): int {
        return $this->getMissingByTeacherQueryBuilder($teacher, $start, $end)
            ->select('COUNT(DISTINCT l.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countMissingByGrade(Grade $grade, DateTime $start, DateTime $end): int {
        return $this->getMissingByGradeQueryBuilder($grade, $start, $end)
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

    private function getMissingQueryBuilder(): QueryBuilder {
        $qbInner = $this->em->createQueryBuilder()
                ->select('lIntern.id')
                ->from(Lesson::class, 'lIntern')
                ->leftJoin('lIntern.entries', 'eIntern')
                ->where('eIntern.id IS NOT NULL')
                ->groupBy('lIntern.id, lIntern.lessonEnd, lIntern.lessonStart')
                ->having('SUM(eIntern.lessonEnd - eIntern.lessonStart + 1) != (lIntern.lessonEnd - lIntern.lessonStart + 1)');

        $qb = $this->em->createQueryBuilder();

        $qb->select(['l', 'e', 't'])
            ->from(Lesson::class, 'l')
            ->leftJoin('l.entries', 'e')
            ->leftJoin('l.tuition', 't')
            ->where(
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
            ->from(Lesson::class, 'lInner')
            ->where('lInner.date >= :start')
            ->andWhere('lInner.date <= :end')
            ->leftJoin('lInner.tuition', 'tInner')
            ->leftJoin('tInner.teacher', 'ttInner')
            ->leftJoin('tInner.additionalTeachers', 'atInner');
        $qbInner
            ->andWhere(
                $qbInner->expr()->orX(
                    'ttInner.id = :teacher',
                    'atInner.id = :teacher'
                )
            );

        $qb->andWhere(
            $qb->expr()->in('l.id', $qbInner->getDQL())
        )
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('teacher', $teacher->getId());

        return $qb;
    }

    /**
     * @inheritDoc
     */
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
            ->from(Lesson::class, 'lInner')
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

    /**
     * @inheritDoc
     */
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
            ->from(Lesson::class, 'lInner')
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

    /**
     * @inheritDoc
     */
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
}