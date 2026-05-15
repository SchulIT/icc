<?php

namespace App\Book\Sorting;

use App\Book\Student\LessonAttendance;
use App\Framework\Sorting\SortingStrategyInterface;

class LessonAttendanceStrategy implements SortingStrategyInterface {

    /**
     * @param LessonAttendance $objectA
     * @param LessonAttendance $objectB
     */
    public function compare($objectA, $objectB): int {
        return $objectA->getLesson() - $objectB->getLesson();
    }
}