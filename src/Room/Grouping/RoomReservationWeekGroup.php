<?php

namespace App\Room\Grouping;

use App\Framework\Date\WeekOfYear;
use App\Framework\Grouping\GroupInterface;
use App\Framework\Grouping\SortableGroupInterface;
use App\Room\Entity\ResourceReservation;

/**
 * @implements SortableGroupInterface<WeekOfYear, ResourceReservation>
 */
class RoomReservationWeekGroup implements SortableGroupInterface {

    /** @var ResourceReservation[] */
    private array $reservations;

    public function __construct(private readonly WeekOfYear $week)
    {
    }

    public function getWeek(): WeekOfYear {
        return $this->week;
    }

    public function getReservations(): array {
        return $this->reservations;
    }

    public function getKey(): WeekOfYear {
        return $this->week;
    }

    public function addItem($item): void {
        $this->reservations[] = $item;
    }

    public function &getItems(): array {
        return $this->reservations;
    }
}