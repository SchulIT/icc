<?php

namespace App\Overtime;

use DateTime;

readonly class OvertimeOverview {

    /**
     * @param Day[] $days
     */
    public function __construct(
        public array $days,
        public DateTime $start,
        public DateTime $end
    ) { }

    /**
     * @return DateTime[]
     */
    public function getDayList(): array {
        $list = [ ];

        foreach($this->days as $day) {
            $list[] = $day->date;
        }

        return $list;
    }

    public function getDay(DateTime $date): ?Day {
        foreach($this->days as $day) {
            if($day->date->format('Y-m-d') == $date->format('Y-m-d')) {
                return $day;
            }
        }

        return null;
    }
}