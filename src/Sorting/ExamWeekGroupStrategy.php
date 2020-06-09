<?php

namespace App\Sorting;

use App\Grouping\ExamWeekGroup;

class ExamWeekGroupStrategy implements SortingStrategyInterface {

    private $weekOfYearStrategy;

    public function __construct(WeekOfYearStrategy $weekOfYearStrategy) {
        $this->weekOfYearStrategy = $weekOfYearStrategy;
    }

    /**
     * @param ExamWeekGroup $objectA
     * @param ExamWeekGroup $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->weekOfYearStrategy->compare($objectA->getWeekOfYear(), $objectB->getWeekOfYear());
    }
}