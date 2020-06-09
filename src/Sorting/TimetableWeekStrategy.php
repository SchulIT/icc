<?php

namespace App\Sorting;

use App\Timetable\TimetableWeek;

class TimetableWeekStrategy implements SortingStrategyInterface {

    private $stringStrategy;

    public function __construct(StringStrategy $strategy) {
        $this->stringStrategy = $strategy;
    }

    /**
     * @param TimetableWeek $objectA
     * @param TimetableWeek $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->stringStrategy->compare($objectA->getWeekName(), $objectB->getWeekName());
    }

}