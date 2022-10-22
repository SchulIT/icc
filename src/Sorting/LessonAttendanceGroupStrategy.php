<?php

namespace App\Sorting;

use App\Grouping\LessonAttendanceCommentsGroup;

class LessonAttendanceGroupStrategy implements SortingStrategyInterface {

    public function __construct(private DateStrategy $dateStrategy)
    {
    }

    /**
     * @param LessonAttendanceCommentsGroup $objectA
     * @param LessonAttendanceCommentsGroup $objectB
     */
    public function compare($objectA, $objectB): int {
        return $this->dateStrategy->compare($objectA->getDate(), $objectB->getDate());
    }
}