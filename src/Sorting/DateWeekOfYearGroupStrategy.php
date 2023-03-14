<?php

namespace App\Sorting;

use App\Grouping\DateWeekOfYearGroup;

class DateWeekOfYearGroupStrategy implements SortingStrategyInterface {

    public function __construct(private readonly DateStrategy $dateStrategy) { }

    /**
     * @param DateWeekOfYearGroup $objectA
     * @param DateWeekOfYearGroup $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->dateStrategy->compare($objectA->getWeek()->getFirstDay(), $objectB->getWeek()->getFirstDay());
    }
}