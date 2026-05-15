<?php

namespace App\Appointment\Sorting;

use App\Appointment\Entity\Appointment;
use App\Framework\Sorting\SortingStrategyInterface;

class AppointmentStrategy implements SortingStrategyInterface {

    /**
     * @param Appointment $objectA
     * @param Appointment $objectB
     */
    public function compare($objectA, $objectB): int {
        if($objectA->getStart() < $objectB->getStart()) {
            return -1;
        } else if($objectA->getStart() == $objectB->getStart()) {
            return 0;
        } else {
            return 1;
        }
    }
}