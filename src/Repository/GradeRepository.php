<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\Section;

class GradeRepository extends AbstractTransactionalRepository implements GradeRepositoryInterface {

    /**
     * @param int $id
     * @return Grade|null
     */
    public function findOneById(int $id): ?Grade {
        return $this->em->getRepository(Grade::class)
            ->findOneBy([
                'id' => $id
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findOneByUuid(string $uuid): ?Grade {
        return $this->em->getRepository(Grade::class)
            ->findOneBy([
                'uuid' => $uuid
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findOneByName(string $name): ?Grade {
        return $this->em->getRepository(Grade::class)
            ->findOneBy([
                'name' => $name
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findOneByExternalId(string $externalId): ?Grade {
        return $this->em->getRepository(Grade::class)
            ->findOneBy([
                'externalId' => $externalId
            ]);
    }

    /**
     * @return Grade[]
     */
    public function findAll() {
        return $this->em->getRepository(Grade::class)
            ->findBy([], [
                'name' => 'asc'
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findAllByExternalId(array $externalIds): array {
        $qb = $this->em->createQueryBuilder();

        $qb
            ->select('g')
            ->from(Grade::class, 'g')
            ->where($qb->expr()->in('g.externalId', ':externalIds'))
            ->setParameter('externalIds', $externalIds);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Grade $grade
     */
    public function persist(Grade $grade): void {
        $this->em->persist($grade);
        $this->flushIfNotInTransaction();
    }

    /**
     * @param Grade $grade
     */
    public function remove(Grade $grade): void {
        $this->em->remove($grade);
        $this->flushIfNotInTransaction();
    }

    /**
     * @inheritDoc
     */
    public function findAllBySection(Section $section): array {
        return $this->em->getRepository(Grade::class)
            ->findBy([
                'section' => $section
            ], [
                'name' => 'asc'
            ]);
    }
}