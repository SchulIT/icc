<?php

namespace App\Sorting;

use App\Grouping\MessageWeekGroup;

class MessageWeekGroupStrategy implements SortingStrategyInterface {

    private $weekOfYearStrategy;

    public function __construct(WeekOfYearStrategy $weekOfYearStrategy) {
        $this->weekOfYearStrategy = $weekOfYearStrategy;
    }

    /**
     * @param MessageWeekGroup $objectA
     * @param MessageWeekGroup $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->weekOfYearStrategy->compare($objectA->getWeek(), $objectB->getWeek());
    }
}