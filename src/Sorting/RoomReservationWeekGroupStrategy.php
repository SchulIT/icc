<?php

namespace App\Sorting;

use App\Grouping\RoomReservationWeekGroup;

class RoomReservationWeekGroupStrategy implements SortingStrategyInterface {

    public function __construct(private WeekOfYearStrategy $weekStrategy)
    {
    }

    /**
     * @param RoomReservationWeekGroup $objectA
     * @param RoomReservationWeekGroup $objectB
     */
    public function compare($objectA, $objectB): int {
        return $this->weekStrategy->compare($objectA->getWeek(), $objectB->getWeek());
    }
}