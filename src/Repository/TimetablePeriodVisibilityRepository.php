<?php

namespace App\Repository;

use App\Entity\TimetablePeriodVisibility;

class TimetablePeriodVisibilityRepository extends AbstractRepository implements TimetablePeriodVisibilityRepositoryInterface {

    /**
     * @inheritDoc
     */
    public function findAll(): array {
        return $this->em->getRepository(TimetablePeriodVisibility::class)
            ->findAll();
    }

    /**
     * @inheritDoc
     */
    public function persist(TimetablePeriodVisibility $periodVisibility): void {
        $this->em->persist($periodVisibility);
        $this->em->flush();
    }
}