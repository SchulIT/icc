<?php

namespace App\Appointment\Sorting;

use App\Appointment\Entity\Appointment;
use App\Framework\Sorting\DateStrategy;
use App\Framework\Sorting\SortingStrategyInterface;

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