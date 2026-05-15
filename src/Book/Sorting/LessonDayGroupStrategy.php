<?php

namespace App\Book\Sorting;

use App\Framework\Sorting\DateStrategy;
use App\Book\Grouping\LessonDayGroup;
use App\Framework\Sorting\SortingStrategyInterface;

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