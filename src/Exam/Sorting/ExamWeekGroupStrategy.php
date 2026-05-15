<?php

namespace App\Exam\Sorting;

use App\Exam\Grouping\ExamWeekGroup;
use App\Framework\Sorting\SortingStrategyInterface;
use App\Framework\Date\Sorting\WeekOfYearStrategy;

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