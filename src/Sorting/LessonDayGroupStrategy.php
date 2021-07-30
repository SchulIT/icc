<?php

namespace App\Sorting;

use App\Grouping\LessonDayGroup;

class LessonDayGroupStrategy implements SortingStrategyInterface {

    private $dateStrategy;

    public function __construct(DateStrategy $dateStrategy) {
        $this->dateStrategy = $dateStrategy;
    }

    /**
     * @param LessonDayGroup $objectA
     * @param LessonDayGroup $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->dateStrategy->compare($objectA->getDate(), $objectB->getDate());
    }
}