<?php

namespace App\Sorting;

use App\Entity\Appointment;

class AppointmentStrategy implements SortingStrategyInterface {

    /**
     * @param Appointment $objectA
     * @param Appointment $objectB
     * @return int
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