<?php

namespace App\Sorting;

use App\Entity\ResourceReservation;

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