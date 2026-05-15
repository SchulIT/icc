<?php

namespace App\Appointment\Sorting;

use App\Appointment\Grouping\AppointmentDateGroup;
use App\Framework\Sorting\SortingStrategyInterface;

class AppointmentDateGroupStrategy implements SortingStrategyInterface {

    /**
     * @param AppointmentDateGroup $objectA
     * @param AppointmentDateGroup $objectB
     */
    public function compare($objectA, $objectB): int {
        $yearDiff = $objectA->getYear() - $objectB->getYear();

        if($yearDiff == 0) {
            $monthDiff = $objectA->getMonth() - $objectB->getMonth();

            return $monthDiff;
        }

        return $yearDiff;
    }
}