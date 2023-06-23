<?php

namespace App\Repository;

use App\Entity\FreeTimespan;
use DateTime;

class FreeTimespanRepository extends AbstractTransactionalRepository implements FreeTimespanRepositoryInterface {

    /**
     * @inheritDoc
     */
    public function findAllByDate(DateTime $dateTime): array {
        return $this->em->getRepository(FreeTimespan::class)
            ->findBy([
                'date' => $dateTime
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array {
        return $this->em->getRepository(FreeTimespan::class)
            ->findAll();
    }

    public function persist(FreeTimespan $timespan): void {
        $this->em->persist($timespan);
        $this->flushIfNotInTransaction();
    }

    public function removeAll(?DateTime $dateTime): void {
        $qb = $this->em->createQueryBuilder()
            ->delete(FreeTimespan::class, 't');

        if($dateTime !== null) {
            $qb->where('t.date = :date')
                ->setParameter('date', $dateTime);
        }

        $qb
            ->getQuery()
            ->execute();

        $this->flushIfNotInTransaction();
    }

    public function removeBetween(DateTime $start, DateTime $end): int {
        return $this->em->createQueryBuilder()
            ->delete(FreeTimespan::class, 't')
            ->where('t.date >= :start')
            ->andWhere('t.date <= :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->execute();
    }
}