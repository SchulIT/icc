<?php

namespace App\Sorting;

use App\Grouping\AppointmentDateGroup;

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