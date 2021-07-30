<?php

namespace App\Sorting;

use App\Book\Student\LessonAttendance;

class LessonAttendanceStrategy implements SortingStrategyInterface {

    /**
     * @param LessonAttendance $objectA
     * @param LessonAttendance $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $objectA->getLesson() - $objectB->getLesson();
    }
}