<?php

namespace App\Grouping;

use App\Entity\ResourceReservation;

class RoomReservationWeekGroup implements GroupInterface, SortableGroupInterface {

    /** @var WeekOfYear */
    private $week;

    /** @var ResourceReservation[] */
    private $reservations;

    public function __construct(WeekOfYear $week) {
        $this->week = $week;
    }

    public function getWeek(): WeekOfYear {
        return $this->week;
    }

    public function getReservations(): array {
        return $this->reservations;
    }

    public function getKey() {
        return $this->week;
    }

    public function addItem($item) {
        $this->reservations[] = $item;
    }

    public function &getItems(): array {
        return $this->reservations;
    }
}