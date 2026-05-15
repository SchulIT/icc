<?php

namespace App\Appointment\Sorting;

use App\Appointment\Entity\AppointmentCategory;
use App\Framework\Sorting\SortingStrategyInterface;
use App\Framework\Sorting\StringStrategy;

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