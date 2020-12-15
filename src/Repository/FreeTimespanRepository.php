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

    public function removeAll(): void {
        $this->em->createQueryBuilder()
            ->delete(FreeTimespan::class, 't')
            ->getQuery()
            ->execute();

        $this->flushIfNotInTransaction();
    }
}