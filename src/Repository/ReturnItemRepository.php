<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\ReturnItem;
use App\Entity\ReturnItemType;
use App\Entity\Student;
use DateTime;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Override;

class ReturnItemRepository extends AbstractRepository implements ReturnItemRepositoryInterface {

    private function getQueryBuilder(int $page, int $limit, ?ReturnItemType $type = null): QueryBuilder {
        $qb = $this->em->createQueryBuilder()
            ->select(['i', 't', 's'])
            ->from(ReturnItem::class, 'i')
            ->leftJoin('i.type', 't')
            ->leftJoin('i.student', 's')
            ->orderBy('i.createdAt', 'desc')
            ->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit);

        if($type !== null) {
            $qb->andWhere('t.id = :type')
                ->setParameter('type', $type->getId());
        }

        return $qb;
    }

    #[Override]
    public function findByStudentsPaginated(array $students, int &$page, int &$limit, ?ReturnItemType $type = null): PaginatedResult {
        if($page < 1) {
            $page = 1;
        }

        if($limit < 1) {
            $limit = 25;
        }

        $studentIds = array_map(fn(Student $student) => $student->getId(), $students);

        $query = $this->getQueryBuilder($page, $limit, $type)
            ->andWhere('s.id IN (:studentIds)')
            ->setParameter('studentIds', $studentIds)
            ->getQuery();

        $paginator = new Paginator($query, fetchJoinCollection: true);

        return new PaginatedResult(
            iterator_to_array($paginator),
            $paginator->count()
        );
    }

    #[Override]
    public function findAllPaginated(int &$page, int &$limit, ?ReturnItemType $type = null): PaginatedResult {
        if($page < 1) {
            $page = 1;
        }

        if($limit < 1) {
            $limit = 25;
        }

        $query = $this->getQueryBuilder($page, $limit, $type)
            ->getQuery();

        $paginator = new Paginator($query, fetchJoinCollection: true);

        return new PaginatedResult(
            iterator_to_array($paginator),
            $paginator->count()
        );
    }

    #[Override]
    public function persist(ReturnItem $returnItem): void {
        $this->em->persist($returnItem);
        $this->em->flush();
    }

    #[Override]
    public function remove(ReturnItem $returnItem): void {
        $this->em->remove($returnItem);
        $this->em->flush();
    }

    #[Override]
    public function countByType(ReturnItemType $type): int {
        return $this->em->createQueryBuilder()
            ->select('COUNT(i)')
            ->from(ReturnItem::class, 'i')
            ->leftJoin('i.type', 't')
            ->where('t.id = :type')
            ->setParameter('type', $type->getId())
            ->getQuery()
            ->getSingleScalarResult();
    }

    #[Override]
    public function countNonReturnedForStudents(array $students): int {
        $studentIds = array_map(fn(Student $student) => $student->getId(), $students);

        return $this->em->createQueryBuilder()
            ->select('COUNT(i)')
            ->from(ReturnItem::class, 'i')
            ->leftJoin('i.student', 's')
            ->where('s.id IN (:studentIds)')
            ->andWhere('i.isReturned = false')
            ->setParameter('studentIds', $studentIds)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findByStudent(Student $student, DateTime $start, DateTime $end): array {
        return $this->em->createQueryBuilder()
            ->select('i')
            ->from(ReturnItem::class, 'i')
            ->leftJoin('i.student', 's')
            ->where('s.id = :studentId')
            ->andwhere('i.createdAt >= :start')
            ->andWhere('i.createdAt <= :end')
            ->setParameter('studentId', $student->getId())
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
    }
}