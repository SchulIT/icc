<?php

namespace App\Sorting;

use App\Grouping\LessonAttendanceCommentsGroup;

class LessonAttendanceGroupStrategy implements SortingStrategyInterface {

    private $dateStrategy;

    public function __construct(DateStrategy $dateStrategy) {
        $this->dateStrategy = $dateStrategy;
    }

    /**
     * @param LessonAttendanceCommentsGroup $objectA
     * @param LessonAttendanceCommentsGroup $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->dateStrategy->compare($objectA->getDate(), $objectB->getDate());
    }
}