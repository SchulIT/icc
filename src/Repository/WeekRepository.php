<?php

namespace App\Repository;

use App\Entity\Week;

class WeekRepository extends AbstractRepository implements WeekRepositoryInterface {

    /**
     * @inheritDoc
     */
    public function findAll(): array {
        return $this->em->getRepository(Week::class)
            ->findBy([], [
                'number' => 'asc'
            ]);
    }
}