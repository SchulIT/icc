<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\SickNote;
use App\Entity\SickNoteReason;
use App\Entity\Student;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class SickNoteRepository extends AbstractRepository implements SickNoteRepositoryInterface {


    public function persist(SickNote $note): void {
        $this->em->persist($note);
        $this->em->flush();
    }

    public function remove(SickNote $note): void {
        $this->em->remove($note);
        $this->em->flush();
    }

    /**
     * @inheritDoc
     */
    public function removeExpired(DateTime $threshold): int {
        return $this->em->createQueryBuilder()
            ->delete(SickNote::class, 's')
            ->where('s.until.date < :threshold')
            ->setParameter('threshold', $threshold)
            ->getQuery()
            ->execute();
    }

    /**
     * @inheritDoc
     */
    public function findByStudents(array $students, ?SickNoteReason $reason = null, ?DateTime $date = null, ?int $lesson = null): array {
        $ids = array_map(function(Student $student) {
            return $student->getId();
        }, $students);

        $qb = $this->em->createQueryBuilder()
            ->select('sn', 's')
            ->from(SickNote::class, 'sn')
            ->leftJoin('sn.student', 's');

        $qb->where($qb->expr()->in('s.id', ':students'))
            ->setParameter('students', $ids);

        $this->applyReasonIfGiven($qb, $reason);

        if($date != null && $lesson != null) {
            $qb->andWhere(
                // start
                $qb->expr()->orX(
                    'sn.from.date < :date',
                    $qb->expr()->andX(
                        'sn.from.date = :date',
                        'sn.from.lesson <= :lesson'
                    )
                )
            );
            $qb->andWhere(
                // end
                $qb->expr()->orX(
                    'sn.until.date > :date',
                    $qb->expr()->andX(
                        'sn.until.date = :date',
                        'sn.until.lesson >= :lesson'
                    )
                )
            );
            $qb->setParameter('date', $date);
            $qb->setParameter('lesson', $lesson);
        } if($date !== null) {
            $this->applyDate($qb, $date);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAll(?DateTime $date = null): array {
        $qb = $this->em->createQueryBuilder()
            ->select('sn', 's')
            ->from(SickNote::class, 'sn')
            ->leftJoin('sn.student', 's');

        if($date !== null) {
            $this->applyDate($qb, $date);
        }

        return $qb->getQuery()->getResult();
    }

    private function applyDate(QueryBuilder $qb, DateTime $date) {
        $qb->andWhere('sn.from.date <= :date');
        $qb->andWhere('sn.until.date >= :date');
        $qb->setParameter('date', $date);
    }

    private function createPaginatorForQueryBuilder(QueryBuilder $queryBuilder, int $itemsPerPage, int &$page): Paginator {
        if(!is_numeric($page) || $page < 1) {
            $page = 1;
        }

        $offset = ($page - 1) * $itemsPerPage;
        $paginator = new Paginator($queryBuilder);
        $paginator->getQuery()
            ->setMaxResults($itemsPerPage)
            ->setFirstResult($offset);

        return $paginator;
    }

    private function applyReasonIfGiven(QueryBuilder $queryBuilder, ?SickNoteReason $reason): QueryBuilder {
        if($reason !== null) {
            $queryBuilder->andWhere('sn.reason = :reason')
                ->setParameter('reason', $reason);
        }

        return $queryBuilder;
    }

    public function getStudentPaginator(Student $student, ?SickNoteReason $reason, int $itemsPerPage, int &$page): Paginator {
        $qb = $this->em->createQueryBuilder()
            ->select('sn', 's')
            ->from(SickNote::class, 'sn')
            ->leftJoin('sn.student', 's');

        $qb->where('s.id = :student')
            ->setParameter('student', $student->getId())
            ->orderBy('sn.until.date', 'desc');

        $this->applyReasonIfGiven($qb, $reason);

        return $this->createPaginatorForQueryBuilder($qb, $itemsPerPage, $page);
    }

    public function getGradePaginator(Grade $grade, Section $section, ?SickNoteReason $reason, int $itemsPerPage, int &$page): Paginator {
        $qb = $this->em->createQueryBuilder()
            ->select('sn', 's')
            ->from(SickNote::class, 'sn')
            ->leftJoin('sn.student', 's')
            ->leftJoin('s.gradeMemberships', 'gm')
            ->leftJoin('gm.section', 'sec')
            ->leftJoin('gm.grade', 'g')
            ->where('g.id = :grade')
            ->andWhere('sec.id = :section')
            ->setParameter('grade', $grade->getId())
            ->setParameter('section', $section->getId())
            ->orderBy('sn.until.date', 'desc');

        $this->applyReasonIfGiven($qb, $reason);

        return $this->createPaginatorForQueryBuilder($qb, $itemsPerPage, $page);
    }

    /**
     * @inheritDoc
     */
    public function getStudentsPaginator(array $students, DateTime $date, ?SickNoteReason $reason, int $itemsPerPage, int &$page): Paginator {
        $ids = array_map(function(Student $student) {
            return $student->getId();
        }, $students);

        $qb = $this->em->createQueryBuilder()
            ->select('sn', 's')
            ->from(SickNote::class, 'sn')
            ->leftJoin('sn.student', 's');

        $qb->where($qb->expr()->in('s.id', ':students'))
            ->setParameter('students', $ids)
            ->andWhere('sn.from.date <= :date')
            ->andWhere('sn.until.date >= :date')
            ->orderBy('sn.until.date', 'desc');

        $this->applyReasonIfGiven($qb, $reason);

        return $this->createPaginatorForQueryBuilder($qb, $itemsPerPage, $page);
    }
}