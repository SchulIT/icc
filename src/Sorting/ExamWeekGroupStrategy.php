<?php

namespace App\Sorting;

use App\Grouping\ExamWeekGroup;

class ExamWeekGroupStrategy implements SortingStrategyInterface {

    public function __construct(private WeekOfYearStrategy $weekOfYearStrategy)
    {
    }

    /**
     * @param ExamWeekGroup $objectA
     * @param ExamWeekGroup $objectB
     */
    public function compare($objectA, $objectB): int {
        return $this->weekOfYearStrategy->compare($objectA->getWeekOfYear(), $objectB->getWeekOfYear());
    }
}