<?php

namespace App\Room\Sorting;

use App\Framework\Sorting\DateStrategy;
use App\Framework\Sorting\SortingStrategyInterface;
use App\Room\Entity\ResourceReservation;

class RoomReservationDateStrategy implements SortingStrategyInterface {

    public function __construct(private DateStrategy $dateStrategy)
    {
    }

    /**
     * @param ResourceReservation $objectA
     * @param ResourceReservation $objectB
     */
    public function compare($objectA, $objectB): int {
        $cmpDate = $this->dateStrategy->compare($objectA->getDate(), $objectB->getDate());

        if($cmpDate === 0) {
            return $objectA->getLessonStart() - $objectB->getLessonStart();
        }

        return $cmpDate;
    }
}