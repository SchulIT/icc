<?php

namespace App\Repository;

use App\Entity\TimetablePeriodVisibility;

interface TimetablePeriodVisibilityRepositoryInterface {

    /**
     * @return TimetablePeriodVisibility[]
     */
    public function findAll(): array;

    /**
     * @param TimetablePeriodVisibility $periodVisibility
     */
    public function persist(TimetablePeriodVisibility $periodVisibility): void;
}