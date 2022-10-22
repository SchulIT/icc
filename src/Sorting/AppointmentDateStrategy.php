<?php

namespace App\Sorting;

use App\Entity\Appointment;

class AppointmentDateStrategy implements SortingStrategyInterface {

    public function __construct(private DateStrategy $dateStrategy)
    {
    }

    /**
     * @param Appointment $objectA
     * @param Appointment $objectB
     */
    public function compare($objectA, $objectB): int {
        return $this->dateStrategy->compare($objectA->getStart(), $objectB->getStart());
    }
}