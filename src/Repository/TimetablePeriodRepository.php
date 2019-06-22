<?php

namespace App\Repository;

use App\Entity\TimetablePeriod;

class TimetablePeriodRepository extends AbstractTransactionalRepository implements TimetablePeriodRepositoryInterface {

    /**
     * @inheritDoc
     */
    public function findOneById(int $id): ?TimetablePeriod {
        return $this->em->getRepository(TimetablePeriod::class)
            ->findOneBy([
                'id' => $id
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findOneByExternalId(string $externalId): ?TimetablePeriod {
        return $this->em->getRepository(TimetablePeriod::class)
            ->findOneBy([
                'externalId' => $externalId
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findAll() {
        return $this->em->getRepository(TimetablePeriod::class)
            ->findBy([], [
                'start' => 'asc'
            ]);
    }

    /**
     * @inheritDoc
     */
    public function persist(TimetablePeriod $period): void {
        $this->em->persist($period);
        $this->flushIfNotInTransaction();
    }

    /**
     * @inheritDoc
     */
    public function remove(TimetablePeriod $period): void {
        $this->em->remove($period);
        $this->flushIfNotInTransaction();
    }

}