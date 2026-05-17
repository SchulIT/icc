<?php

namespace App\Common\Repository;

use App\Framework\Repository\AbstractTransactionalRepository;
use App\Common\Entity\Subject;
use App\Common\Repository\SubjectRepositoryInterface;
use App\Framework\Repository\PaginatedResult;
use App\Framework\Repository\PaginationQuery;

class SubjectRepository extends AbstractTransactionalRepository implements SubjectRepositoryInterface {

    public function findOneById(int $id): ?Subject {
        return $this->em->getRepository(Subject::class)
            ->findOneBy([
                'id'=> $id
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findOneByUuid(string $uuid): ?Subject {
        return $this->em->getRepository(Subject::class)
            ->findOneBy([
                'uuid'=> $uuid
            ]);
    }

    public function findOneByAbbreviation(string $abbreviation): ?Subject {
        return $this->em->getRepository(Subject::class)
            ->findOneBy([
                'abbreviation' => $abbreviation
            ]);
    }

    public function findAllWithTeachers(): array {
        $qb = $this->em->createQueryBuilder();

        return $qb
            ->select(['s'])
            ->from(Subject::class, 's')
            ->where(
                $qb->expr()->in(
                    's.id',
                        $this->em->createQueryBuilder()
                            ->select('sInner')
                            ->from(Subject::class, 'sInner')
                            ->innerJoin('sInner.teachers', 'tInner')
                            ->getQuery()->getDQL()
                    )
            )
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Subject[]
     */
    public function findAll(bool $onlyExternal = false): array {
        $subjects = $this->em->getRepository(Subject::class)
            ->findAll();

        if($onlyExternal === true) {
            $subjects = array_filter($subjects, fn(Subject $subject) => $subject->getExternalId() !== null);
        }

        return $subjects;
    }

    public function findPaginated(PaginationQuery $paginationQuery): PaginatedResult {
        $qb = $this->em->createQueryBuilder()
            ->select(['s'])
            ->from(Subject::class, 's')
            ->orderBy('s.abbreviation', 'ASC');

        return PaginatedResult::fromQueryBuilder($qb, $paginationQuery);
    }

    /**
     * @inheritDoc
     */
    public function findAllByExternalId(array $externalIds): array {
        $qb = $this->em->createQueryBuilder();

        return $qb
            ->select(['s'])
            ->from(Subject::class, 's')
            ->where($qb->expr()->in('s.externalId', ':ids'))
            ->setParameter('ids', $externalIds)
            ->getQuery()
            ->getResult();
    }

    public function persist(Subject $subject): void {
        $this->em->persist($subject);
        $this->flushIfNotInTransaction();
    }

    public function remove(Subject $subject): void {
        $this->em->remove($subject);
        $this->flushIfNotInTransaction();
    }
}