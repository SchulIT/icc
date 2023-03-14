<?php

namespace App\Grouping;

use App\Date\WeekOfYear;
use DateTime;
use JsonSerializable;

class DateWeekOfYearGroup implements GroupInterface, SortableGroupInterface, JsonSerializable {

    /** @var DateTime[] */
    private array $days;

    public function __construct(private readonly WeekOfYear $week) {

    }

    public function getWeek(): WeekOfYear {
        return $this->week;
    }

    /**
     * @return DateTime[]
     */
    public function getDays(): array {
        return $this->days;
    }

    public function getKey() {
        return $this->week;
    }

    public function addItem($item) {
        $this->days[] = $item;
    }

    public function &getItems(): array {
        return $this->days;
    }

    public function jsonSerialize(): array {
        return [
            'week' => [
                'weekNumber' => $this->week->getWeekNumber(),
                'year' => $this->week->getYear()
            ],
            'days' => array_map(fn(DateTime $dateTime) => $dateTime->format('c'), $this->days)
        ];
    }
}