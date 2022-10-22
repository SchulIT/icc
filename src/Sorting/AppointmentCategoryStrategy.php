<?php

namespace App\Sorting;

use App\Entity\AppointmentCategory;

class AppointmentCategoryStrategy implements SortingStrategyInterface {

    public function __construct(private StringStrategy $stringStrategy)
    {
    }

    /**
     * @param AppointmentCategory $objectA
     * @param AppointmentCategory $objectB
     */
    public function compare($objectA, $objectB): int {
        return $this->stringStrategy->compare($objectA->getName(), $objectB->getName());
    }
}