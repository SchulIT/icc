<?php

namespace App\Repository;

use App\Entity\Subject;

class SubjectRepository extends AbstractTransactionalRepository implements SubjectRepositoryInterface {

    /**
     * @param int $id
     * @return Subject|null
     */
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

    /**
     * @param string $abbreviation
     * @return Subject|null
     */
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
    public function findAll() {
        return $this->em->getRepository(Subject::class)
            ->findAll();
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

    /**
     * @param Subject $subject
     */
    public function persist(Subject $subject): void {
        $this->em->persist($subject);
        $this->flushIfNotInTransaction();
    }

    /**
     * @param Subject $subject
     */
    public function remove(Subject $subject): void {
        $this->em->remove($subject);
        $this->flushIfNotInTransaction();
    }
}