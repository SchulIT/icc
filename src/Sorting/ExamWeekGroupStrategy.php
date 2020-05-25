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
        if($objectA->getWeekOfYear() === null && $objectB->getWeekOfYear() === null) {
            return true;
        } else if($objectA->getWeekOfYear() === null) {
            return -1;
        } else if($objectB->getWeekOfYear() === null) {
            return 1;
        }

        if($objectA->getWeekOfYear()->getYear() === $objectB->getWeekOfYear()->getYear()) {
            return $objectA->getWeekOfYear()->getWeekNumber() - $objectB->getWeekOfYear()->getWeekNumber();
        }

        return $objectA->getWeekOfYear()->getYear() - $objectB->getWeekOfYear()->getYear();
    }
}