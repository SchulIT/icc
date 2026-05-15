<?php

namespace App\Timetable\Repository;

use App\Framework\Repository\AbstractRepository;
use App\Timetable\Repository\WeekRepositoryInterface;
use App\Timetable\Entity\Week;

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