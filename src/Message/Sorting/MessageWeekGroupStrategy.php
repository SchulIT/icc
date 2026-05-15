<?php

namespace App\Message\Sorting;

use App\Framework\Sorting\SortingStrategyInterface;
use App\Message\Grouping\MessageWeekGroup;
use App\Framework\Date\Sorting\WeekOfYearStrategy;

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