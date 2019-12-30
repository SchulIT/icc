<?php

namespace App\Repository;

use App\Entity\Absence;

class AbsenceRepository extends AbstractTransactionalRepository implements AbsenceRepositoryInterface {

    public function findAll(): array {
        return $this->em
            ->getRepository(Absence::class)
            ->findAll();
    }

    /**
     * @inheritDoc
     */
    public function findAllTeachers(\DateTime $date): array {
        $qb = $this->em->createQueryBuilder();

        $qb
            ->select(['p', 't'])
            ->from(Absence::class, 'p')
            ->leftJoin('p.teacher', 't')
            ->where($qb->expr()->isNotNull('t.id'))
            ->andWhere('p.date = :date')
            ->setParameter('date', $date);

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllStudyGroups(\DateTime $date): array {
        $qb = $this->em->createQueryBuilder();

        $qb
            ->select(['p', 't'])
            ->from(Absence::class, 'p')
            ->leftJoin('p.studyGroup', 'sg')
            ->where($qb->expr()->isNotNull('sg.id'))
            ->andWhere('p.date = :date')
            ->setParameter('date', $date);

        return $qb->getQuery()->getResult();
    }

    public function persist(Absence $person): void {
        $this->em->persist($person);
        $this->flushIfNotInTransaction();
    }

    public function removeAll(): void {
        $this->em->createQueryBuilder()
            ->delete('p')
            ->from(Absence::class, 'p')
            ->getQuery()
            ->execute();

        $this->flushIfNotInTransaction();
    }
}