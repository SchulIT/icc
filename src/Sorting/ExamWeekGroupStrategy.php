<?php

namespace App\Sorting;

use App\Grouping\ExamWeekGroup;

class ExamWeekGroupStrategy implements SortingStrategyInterface {

    /**
     * @param ExamWeekGroup $objectA
     * @param ExamWeekGroup $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $objectA->getWeek() - $objectB->getWeek();
    }
}