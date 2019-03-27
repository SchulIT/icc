<?php

namespace App\Sorting;

use App\Entity\TimetablePeriod;

class TimetablePeriodStrategy implements SortingStrategyInterface {

    /**
     * @param TimetablePeriod $objectA
     * @param TimetablePeriod $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        if($objectA->getStart() == $objectB->getStart()) {
            return 0;
        }

        return $objectA->getStart() < $objectB->getStart() ? -1 : 1;
    }
}