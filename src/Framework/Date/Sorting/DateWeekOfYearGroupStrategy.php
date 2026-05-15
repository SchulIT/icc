<?php

namespace App\Framework\Date\Sorting;

use App\Framework\Sorting\DateStrategy;
use App\Framework\Date\Grouping\DateWeekOfYearGroup;
use App\Framework\Sorting\SortingStrategyInterface;

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