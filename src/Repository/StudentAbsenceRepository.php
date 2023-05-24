<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\StudentAbsence;
use App\Entity\Student;
use App\Entity\StudentAbsenceType;
use DateTime;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class StudentAbsenceRepository extends AbstractRepository implements StudentAbsenceRepositoryInterface {

    public function persist(StudentAbsence $note): void {
        $this->em->persist($note);
        $this->em->flush();
    }

    public function remove(StudentAbsence $note): void {
        $this->em->remove($note);
        $this->em->flush();
    }

    /**
     * @inheritDoc
     */
    public function removeExpired(DateTime $threshold): int {
        return $this->em->createQueryBuilder()
            ->delete(StudentAbsence::class, 's')
            ->where('s.until.date < :threshold')
            ->setParameter('threshold', $threshold)
            ->getQuery()
            ->execute();
    }

    /**
     * @inheritDoc
     */
    public function findByStudents(array $students, ?StudentAbsenceType $type = null, ?DateTime $date = null, ?int $lesson = null): array {
        $ids = array_map(fn(Student $student) => $student->getId(), $students);

        $qb = $this->em->createQueryBuilder()
            ->select('sn', 's')
            ->from(StudentAbsence::class, 'sn')
            ->leftJoin('sn.student', 's');

        $qb->where($qb->expr()->in('s.id', ':students'))
            ->setParameter('students', $ids);

        $this->applyTypeIfGiven($qb, $type);

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
            ->from(StudentAbsence::class, 'sn')
            ->leftJoin('sn.student', 's');

        if($date !== null) {
            $this->applyDate($qb, $date);
        }

        return $qb->getQuery()->getResult();
    }

    public function findAllUuids(DateTime $start, DateTime $end): array {
        return
            array_map(
                fn($row) => $row['uuid'],
                $this->em->createQueryBuilder()
                ->select('a.uuid')
                ->from(StudentAbsence::class, 'a')
                ->where('a.from.date >= :start')
                ->andWhere('a.until.date <= :end')
                ->setParameter('start', $start)
                ->setParameter('end', $end)
                ->getQuery()
                ->getScalarResult()
            );
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

    private function applyTypeIfGiven(QueryBuilder $queryBuilder, ?StudentAbsenceType $type): void {
        if($type !== null) {
            $queryBuilder->leftJoin('sn.type', 't')
                ->andWhere('t.id = :type')
                ->setParameter('type', $type->getId());
        }

    }

    public function getPaginator(?StudentAbsenceType $type, int $itemsPerPage, int &$page): Paginator {
        $qb = $this->em->createQueryBuilder()
            ->select('sn', 's')
            ->from(StudentAbsence::class, 'sn')
            ->leftJoin('sn.student', 's')
            ->orderBy('sn.until.date', 'desc');

        $this->applyTypeIfGiven($qb, $type);

        return $this->createPaginatorForQueryBuilder($qb, $itemsPerPage, $page);
    }

    public function getStudentPaginator(Student $student, ?StudentAbsenceType $type, int $itemsPerPage, int &$page): Paginator {
        $qb = $this->em->createQueryBuilder()
            ->select('sn', 's')
            ->from(StudentAbsence::class, 'sn')
            ->leftJoin('sn.student', 's');

        $qb->where('s.id = :student')
            ->setParameter('student', $student->getId())
            ->orderBy('sn.until.date', 'desc');

        $this->applyTypeIfGiven($qb, $type);

        return $this->createPaginatorForQueryBuilder($qb, $itemsPerPage, $page);
    }

    public function getGradePaginator(Grade $grade, Section $section, ?StudentAbsenceType $type, int $itemsPerPage, int &$page): Paginator {
        $qb = $this->em->createQueryBuilder()
            ->select('sn', 's')
            ->from(StudentAbsence::class, 'sn')
            ->leftJoin('sn.student', 's')
            ->leftJoin('s.gradeMemberships', 'gm')
            ->leftJoin('gm.section', 'sec')
            ->leftJoin('gm.grade', 'g')
            ->where('g.id = :grade')
            ->andWhere('sec.id = :section')
            ->setParameter('grade', $grade->getId())
            ->setParameter('section', $section->getId())
            ->orderBy('sn.until.date', 'desc');

        $this->applyTypeIfGiven($qb, $type);

        return $this->createPaginatorForQueryBuilder($qb, $itemsPerPage, $page);
    }

    /**
     * @inheritDoc
     */
    public function getStudentsPaginator(array $students, DateTime $date, ?StudentAbsenceType $type, int $itemsPerPage, int &$page): Paginator {
        $ids = array_map(fn(Student $student) => $student->getId(), $students);

        $qb = $this->em->createQueryBuilder()
            ->select('sn', 's')
            ->from(StudentAbsence::class, 'sn')
            ->leftJoin('sn.student', 's');

        $qb->where($qb->expr()->in('s.id', ':students'))
            ->setParameter('students', $ids)
            ->andWhere('sn.from.date <= :date')
            ->andWhere('sn.until.date >= :date')
            ->orderBy('sn.until.date', 'desc');

        $this->applyTypeIfGiven($qb, $type);

        return $this->createPaginatorForQueryBuilder($qb, $itemsPerPage, $page);
    }
}