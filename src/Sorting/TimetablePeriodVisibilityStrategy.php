<?php

namespace App\Sorting;

use App\Entity\TimetablePeriodVisibility;

class TimetablePeriodVisibilityStrategy implements SortingStrategyInterface {

    private $userTypeStrategy;

    public function __construct(UserTypeStrategy $userTypeStrategy) {
        $this->userTypeStrategy = $userTypeStrategy;
    }

    /**
     * @param TimetablePeriodVisibility $objectA
     * @param TimetablePeriodVisibility $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->userTypeStrategy->compare($objectA->getUserType(), $objectB->getUserType());
    }
}