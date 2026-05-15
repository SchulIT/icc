<?php

namespace App\Book\Sorting;

use App\Framework\Sorting\DateStrategy;
use App\Book\Grouping\LessonAttendanceCommentsGroup;
use App\Framework\Sorting\SortingStrategyInterface;

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