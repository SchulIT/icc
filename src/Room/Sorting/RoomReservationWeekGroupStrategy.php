<?php

namespace App\Room\Sorting;

use App\Framework\Sorting\SortingStrategyInterface;
use App\Room\Grouping\RoomReservationWeekGroup;
use App\Framework\Date\Sorting\WeekOfYearStrategy;

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