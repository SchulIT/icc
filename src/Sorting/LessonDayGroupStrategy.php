<?php

namespace App\Sorting;

use App\Grouping\LessonDayGroup;

class LessonDayGroupStrategy implements SortingStrategyInterface {

    public function __construct(private DateStrategy $dateStrategy)
    {
    }

    /**
     * @param LessonDayGroup $objectA
     * @param LessonDayGroup $objectB
     */
    public function compare($objectA, $objectB): int {
        return $this->dateStrategy->compare($objectA->getDate(), $objectB->getDate());
    }
}