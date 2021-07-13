<?php

namespace App\Sorting;

use App\Grouping\LessonAttendanceGroup;

class LessonAttendanceGroupStrategy implements SortingStrategyInterface {

    private $dateStrategy;

    public function __construct(DateStrategy $dateStrategy) {
        $this->dateStrategy = $dateStrategy;
    }

    /**
     * @param LessonAttendanceGroup $objectA
     * @param LessonAttendanceGroup $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->dateStrategy->compare($objectA->getDate(), $objectB->getDate());
    }
}