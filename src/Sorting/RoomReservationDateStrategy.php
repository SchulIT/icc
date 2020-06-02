<?php

namespace App\Sorting;

use App\Entity\RoomReservation;

class RoomReservationDateStrategy implements SortingStrategyInterface {

    private $dateStrategy;

    public function __construct(DateStrategy $dateStrategy) {
        $this->dateStrategy = $dateStrategy;
    }

    /**
     * @param RoomReservation $objectA
     * @param RoomReservation $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        $cmpDate = $this->dateStrategy->compare($objectA->getDate(), $objectB->getDate());

        if($cmpDate === 0) {
            return $objectA->getLessonStart() - $objectB->getLessonStart();
        }

        return $cmpDate;
    }
}