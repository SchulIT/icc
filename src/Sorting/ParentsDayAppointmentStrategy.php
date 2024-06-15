<?php

namespace App\Sorting;

use App\Entity\ParentsDayAppointment;

class ParentsDayAppointmentStrategy implements SortingStrategyInterface {

    public function __construct(private readonly DateStrategy $dateStrategy) {

    }

    /**
     * @param ParentsDayAppointment $objectA
     * @param ParentsDayAppointment $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->dateStrategy->compare($objectA->getStartDateTime(), $objectB->getStartDateTime());
    }
}