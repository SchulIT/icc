<?php

namespace App\Sorting;

use App\Entity\AppointmentCategory;

class AppointmentCategoryStrategy implements SortingStrategyInterface {

    private $stringStrategy;

    public function __construct(StringStrategy $strategy) {
        $this->stringStrategy = $strategy;
    }

    /**
     * @param AppointmentCategory $objectA
     * @param AppointmentCategory $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->stringStrategy->compare($objectA->getName(), $objectB->getName());
    }
}