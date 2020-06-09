<?php

namespace App\Sorting;

use App\Grouping\RoomReservationWeekGroup;

class RoomReservationWeekGroupStrategy implements SortingStrategyInterface {

    private $weekStrategy;

    public function __construct(WeekOfYearStrategy $weekStrategy) {
        $this->weekStrategy = $weekStrategy;
    }

    /**
     * @param RoomReservationWeekGroup $objectA
     * @param RoomReservationWeekGroup $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->weekStrategy->compare($objectA->getWeek(), $objectB->getWeek());
    }
}