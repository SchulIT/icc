<?php

namespace App\Sorting;

use App\Grouping\MessageWeekGroup;

class MessageWeekGroupStrategy implements SortingStrategyInterface {

    public function __construct(private WeekOfYearStrategy $weekOfYearStrategy)
    {
    }

    /**
     * @param MessageWeekGroup $objectA
     * @param MessageWeekGroup $objectB
     */
    public function compare($objectA, $objectB): int {
        return $this->weekOfYearStrategy->compare($objectA->getWeek(), $objectB->getWeek());
    }
}