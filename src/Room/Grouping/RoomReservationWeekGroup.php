<?php

namespace App\Room\Grouping;

use App\Framework\Date\WeekOfYear;
use App\Framework\Grouping\GroupInterface;
use App\Framework\Grouping\SortableGroupInterface;
use App\Room\Entity\ResourceReservation;

class RoomReservationWeekGroup implements GroupInterface, SortableGroupInterface {

    /** @var ResourceReservation[] */
    private $reservations;

    public function __construct(private WeekOfYear $week)
    {
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