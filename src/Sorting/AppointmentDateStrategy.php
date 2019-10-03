<?php

namespace App\Sorting;

use App\Entity\Appointment;

class AppointmentDateStrategy implements SortingStrategyInterface {

    private $dateStrategy;

    public function __construct(DateStrategy $dateStrategy) {
        $this->dateStrategy = $dateStrategy;
    }

    /**
     * @param Appointment $objectA
     * @param Appointment $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->dateStrategy->compare($objectA->getStart(), $objectB->getStart());
    }
}