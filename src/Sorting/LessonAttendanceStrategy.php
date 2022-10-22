<?php

namespace App\Sorting;

use App\Book\Student\LessonAttendance;

class LessonAttendanceStrategy implements SortingStrategyInterface {

    /**
     * @param LessonAttendance $objectA
     * @param LessonAttendance $objectB
     */
    public function compare($objectA, $objectB): int {
        return $objectA->getLesson() - $objectB->getLesson();
    }
}