<?php

namespace App\Sorting;

use App\Grouping\WeekOfYear;

class WeekOfYearStrategy implements SortingStrategyInterface {

    /**
     * @param WeekOfYear $objectA
     * @param WeekOfYear $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        if($objectA === null && $objectB === null) {
            return true;
        } else if($objectA === null) {
            return -1;
        } else if($objectB === null) {
            return 1;
        }

        if($objectA->getYear() === $objectB->getYear()) {
            return $objectA->getWeekNumber() - $objectB->getWeekNumber();
        }

        return $objectA->getYear() - $objectB->getYear();
    }
}